<?php

class GestorPedido {
    public function __construct(
        private readonly UnidadeTrabalho $unidadeTrabalho,
        private readonly RepositorioPedido $repositorioPedido,
        private readonly RepositorioCliente $repositorioCliente,
        private readonly RepositorioPizza $repositorioPizza,
        private readonly RepositorioProdutoSimples $repositorioProdutoSimples,
        private readonly RepositorioIngrediente $repositorioIngrediente,
        private readonly RepositorioPromocao $repositorioPromocao,
        private readonly RepositorioConfiguracaoFidelidade $repositorioConfiguracaoFidelidade,
        private readonly RepositorioMovimentacaoEstoque $repositorioMovimentacaoEstoque,
        private readonly RepositorioEntregador $repositorioEntregador,
    ) {}

    /**
     * @return PedidoParaListagem[]
     */
    public function obterPedidosParaListagem(?int $idPedido = null): array {
        return $this->repositorioPedido->obterPedidosParaListagem($idPedido);
    }

    public function alterarStatus(int $idPedido, string $novoStatus): void {
        $status = StatusPedido::tryFrom($novoStatus);
        if ($status === null) {
            throw new DominioException("Status de pedido inválido: {$novoStatus}.");
        }

        $this->repositorioPedido->alterarStatus($idPedido, $status);
    }

    public function registrarPedido(PedidoParaCadastro $pedidoParaCadastro): Pedido {
        $pedido = $this->criarPedido($pedidoParaCadastro);
        $this->validarEntregaPedido($pedido);
        $this->validarEstoqueItensPedido($pedido->itensPedido);

        $this->unidadeTrabalho->executar(function () use ($pedido) {
            $this->repositorioPedido->registrarPedido($pedido);
            $this->atualizarEstoqueItensPedido($pedido);
            $this->marcarBeneficioFidelidadeComoUtilizado($pedido);
            $this->gerarNovoBeneficioFidelidade($pedido);
        });

        return $pedido;
    }

    public function obterResumoValores(PedidoParaCadastro $pedidoParaCadastro): ResumoValoresPedido {
        $pedido = $this->criarPedido($pedidoParaCadastro);

        return ResumoValoresPedido::criarComEntidade($pedido);
    }

    private function criarPedido(PedidoParaCadastro $pedidoParaCadastro): Pedido {
        $tipoAtendimento = TipoAtendimento::tryFrom($pedidoParaCadastro->tipoAtendimento);
        if ($tipoAtendimento === null) {
            throw DominioException::criarException("Tipo de atendimento inválido: {$pedidoParaCadastro->tipoAtendimento}.");
        }

        $erros = [];
        $itensPedido = $this->criarItensPedido(itensPedidoParaCadastro: $pedidoParaCadastro->itensPedido, erros: $erros);
        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        $descontosPromocao = $this->criarDescontosPromocao($itensPedido);

        $descontoFidelidade = null;
        $cliente = $this->repositorioCliente->obterClientePorId($pedidoParaCadastro->idCliente);
        if ($cliente instanceof Cliente && $cliente->temBeneficioFidelidadeValidoParaUso()) {
            $descontoFidelidade = $this->criarDescontoFidelidade(
                beneficioFidelidade: $cliente->beneficioFidelidade,
                itensPedido: $itensPedido,
                descontosPromocao: $descontosPromocao,
            );
        }

        $entrega = $this->criarEntrega(
            tipoAtendimento: $tipoAtendimento,
            cliente: $cliente,
            idEntregador: $pedidoParaCadastro->idEntregador,
            erros: $erros,
        );
        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        return new Pedido(
            id: 0,
            usuario: UsuarioFactory::atendente(), // TODO: Temporário para passar os testes
            data: new DateTime(),
            tipoAtendimento: $tipoAtendimento,
            cliente: $cliente,
            itensPedido: $itensPedido,
            status: StatusPedido::inicial(),
            descontosPromocao: $descontosPromocao,
            descontoFidelidade: $descontoFidelidade,
            entrega: $entrega,
        );
    }

    /**
     * @param ItemPizzaParaCadastro[]|ItemSimplesParaCadastro[] $itensPedidoParaCadastro
     * @param string[] $erros
     * @return ItemPedido[]
     */
    private function criarItensPedido(array $itensPedidoParaCadastro, array &$erros): array {
        $itensPizza = [];
        $itensSimples = [];

        foreach ($itensPedidoParaCadastro as $itemPedidoParaCadastro) {
            if ($itemPedidoParaCadastro instanceof ItemPizzaParaCadastro) {
                $itemPizza = $this->criarItemPizza(itemPizzaParaCadastro: $itemPedidoParaCadastro, erros: $erros);
                if ($itemPizza instanceof ItemPizza) {
                    $itensPizza[] = $itemPizza;
                }

                continue;
            }

            $itemSimples = $this->criarItemSimples(itemSimplesParaCadastro: $itemPedidoParaCadastro, erros: $erros);
            if ($itemSimples instanceof ItemSimples) {
                $itensSimples[] = $itemSimples;
            }
        }

        $itensPedido = [...$itensPizza, ...$itensSimples];
        if (empty($erros) && empty($itensPedido)) {
            $erros[] = 'O pedido deve conter pelo menos um item.';
        }

        return $itensPedido;
    }

    /**
     * @param string[] $erros
     */
    private function criarItemPizza(ItemPizzaParaCadastro $itemPizzaParaCadastro, array &$erros): ?ItemPizza {
        $tamanhoPizza = TamanhoPizza::tryFrom($itemPizzaParaCadastro->tamanho);
        if ($tamanhoPizza === null) {
            $erros[] = "Tamanho de pizza inválido: {$itemPizzaParaCadastro->tamanho}.";

            return null;
        }

        $saboresEscolhidos = $this->criarSaboresEscolhidos(
            saboresEscolhidosParaCadastro: $itemPizzaParaCadastro->saboresEscolhidos,
            erros: $erros,
        );

        $itemPizza = null;

        try {
            $itemPizza = new ItemPizza(
                id: 0,
                quantidade: $itemPizzaParaCadastro->quantidade,
                saboresEscolhidos: $saboresEscolhidos,
                tamanhoPizza: $tamanhoPizza,
            );
        } catch (DominioException $e) {
            $erros = [...$erros, ...$e->erros()];
        }

        return $itemPizza;
    }

    /**
     * @param SaborEscolhidoParaCadastro[] $saboresEscolhidosParaCadastro
     * @param string[] $erros
     * @return SaborEscolhido[]
     */
    private function criarSaboresEscolhidos(array $saboresEscolhidosParaCadastro, array &$erros): array {
        $erros = [];
        $saboresEscolhidos = [];

        $quantidadeSabores = count($saboresEscolhidosParaCadastro);

        foreach ($saboresEscolhidosParaCadastro as $saborEscolhidoParaCadastro) {
            $pizza = $this->repositorioPizza->obterPizzaPorId($saborEscolhidoParaCadastro->idPizza);
            if (!$pizza instanceof Pizza) {
                $erros[] = "Pizza (ID {$saborEscolhidoParaCadastro->idPizza}) não encontrada.";
                continue;
            }

            $ingredientesEscolhidos = $this->criarIngredientesEscolhidos(
                pizza: $pizza,
                quantidadeSabores: $quantidadeSabores,
                ingredientesEscolhidosParaCadastro: $saborEscolhidoParaCadastro->ingredientesEscolhidos,
                erros: $erros,
            );

            try {
                $saborEscolhido = new SaborEscolhido(
                    id: 0,
                    pizza: $pizza,
                    ingredientesEscolhidos: $ingredientesEscolhidos,
                );
            } catch (DominioException $e) {
                $erros = [...$erros, ...$e->erros()];
                continue;
            }

            $saboresEscolhidos[] = $saborEscolhido;
        }

        return $saboresEscolhidos;
    }

    /**
     * @param IngredienteEscolhidoParaCadastro[] $ingredientesEscolhidosParaCadastro
     * @param string[] $erros
     * @return IngredienteEscolhido[]
     */
    private function criarIngredientesEscolhidos(Pizza $pizza, int $quantidadeSabores, array $ingredientesEscolhidosParaCadastro, array &$erros): array {
        $erros = [];
        $ingredientesEscolhidos = [];

        foreach ($ingredientesEscolhidosParaCadastro as $ingredienteEscolhidoParaCadastro) {
            $ingrediente = $this->repositorioIngrediente->obterIngredientePorId($ingredienteEscolhidoParaCadastro->idIngrediente);
            if (!$ingrediente instanceof Ingrediente) {
                $erros[] = "Ingrediente (ID {$ingredienteEscolhidoParaCadastro->idIngrediente}) não encontrado.";
                continue;
            }

            $papel = PapelIngrediente::tryFrom(value: $ingredienteEscolhidoParaCadastro->papel);
            if ($papel === null) {
                $erros[] = "Papel do ingrediente inválido: {$ingredienteEscolhidoParaCadastro->papel}.";
                continue;
            }

            $custo = Dinheiro::zero();
            if ($papel === PapelIngrediente::ADICIONAL) {
                $adicional = $pizza->obterAdicional($ingrediente);
                if (!$adicional instanceof IngredienteAdicional) {
                    $erros[] = "Ingrediente adicional (ID {$ingrediente->id}) não encontrado para a pizza (ID {$pizza->id}).";
                    continue;
                }

                $custo = $adicional->preco->dividir($quantidadeSabores);
            }

            try {
                $ingredienteEscolhido = new IngredienteEscolhido(
                    id: 0,
                    ingrediente: $ingrediente,
                    papel: $papel,
                    custoAdicional: $custo,
                );
            } catch (DominioException $e) {
                $erros = [...$erros, ...$e->erros()];
                continue;
            }

            $ingredientesEscolhidos[] = $ingredienteEscolhido;
        }

        return $ingredientesEscolhidos;
    }

    /**
     * @param string[] $erros
     */
    private function criarItemSimples(ItemSimplesParaCadastro $itemSimplesParaCadastro, array &$erros): ?ItemSimples {
        $produtoSimples = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($itemSimplesParaCadastro->idProduto);
        if (!$produtoSimples instanceof ProdutoSimples) {
            $erros[] = "Produto simples (ID {$itemSimplesParaCadastro->idProduto}) não encontrado.";

            return null;
        }

        $itemSimples = null;

        try {
            $itemSimples = new ItemSimples(
                id: 0,
                quantidade: $itemSimplesParaCadastro->quantidade,
                produtoSimples: $produtoSimples,
            );
        } catch (DominioException $e) {
            $erros = [...$erros, ...$e->erros()];
        }

        return $itemSimples;
    }

    /**
     * @param ItemPedido[] $itensPedido
     * @return DescontoPromocao[]
     */
    private function criarDescontosPromocao(array $itensPedido): array {
        $promocoesVigentes = $this->repositorioPromocao->obterPromocoes(new FiltroPromocao(apenasVigentes: true));
        $itensPizza = array_filter($itensPedido, function (ItemPedido $itemPedido) {
            return $itemPedido instanceof ItemPizza;
        });

        return new CalculadoraDescontoPromocao()->calcularDescontos(itensPizza: $itensPizza, promocoes: $promocoesVigentes);
    }

    /**
     * @param ItemPedido[] $itensPedido
     * @param DescontoPromocao[] $descontosPromocao
     */
    private function criarDescontoFidelidade(BeneficioFidelidade $beneficioFidelidade, array $itensPedido, array $descontosPromocao): ?DescontoFidelidade {
        $valorTotalDescontoPromocao = Dinheiro::zero();

        foreach ($descontosPromocao as $descontoPromocao) {
            $valorTotalDescontoPromocao = $valorTotalDescontoPromocao->somar($descontoPromocao->valorDesconto);
        }

        return new CalculadoraDescontoFidelidade()->calcularDesconto(
            itensPedido: $itensPedido,
            beneficioFidelidade: $beneficioFidelidade,
            valorTotalDescontoPromocao: $valorTotalDescontoPromocao,
        );
    }

    private function validarEntregaPedido(Pedido $pedido): void {
        if ($pedido->tipoAtendimento === TipoAtendimento::ENTREGA && !$pedido->entrega instanceof Entrega) {
            throw DominioException::criarException('O entregador é obrigatório para pedidos de entrega.');
        }
    }

    /**
     * @param ItemPedido[] $itensPedido
     */
    private function validarEstoqueItensPedido(array $itensPedido): void {
        $erros = [];

        $validador = new ValidadorEstoqueItemPedido();
        $validador->validarEstoqueItensPedido(itensPedido: $itensPedido, erros: $erros);

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }

    /**
     * @param string[] $erros
     */
    private function criarEntrega(TipoAtendimento $tipoAtendimento, ?Cliente $cliente, string $idEntregador, array &$erros): ?Entrega {
        if ($tipoAtendimento !== TipoAtendimento::ENTREGA || $idEntregador === '') {
            return null;
        }

        $entregador = $this->repositorioEntregador->obterEntregadorPorId((int) $idEntregador);
        if (!$entregador instanceof Entregador) {
            $erros[] = "Entregador (ID {$idEntregador}) não encontrado.";

            return null;
        }

        if (!$entregador->disponivel) {
            $erros[] = "O entregador {$entregador->nome} (ID {$entregador->id}) não está disponível.";

            return null;
        }

        if (!$cliente instanceof Cliente) {
            return null;
        }

        return new Entrega(
            id: 0,
            endereco: $cliente->endereco,
            entregador: $entregador,
            dataSaida: null,
            dataChegada: null,
            taxa: new Dinheiro(Entrega::TAXA_ENTREGA_FIXA_EM_CENTAVOS),
        );
    }

    private function atualizarEstoqueItensPedido(Pedido $pedido): void {
        foreach ($pedido->itensPedido as $itemPedido) {
            if ($itemPedido instanceof ItemPizza) {
                $this->atualizarEstoqueItemPizza(pedido: $pedido, itemPizza: $itemPedido);
                continue;
            }

            if ($itemPedido instanceof ItemSimples) {
                $this->atualizarEstoqueItemSimples(pedido: $pedido, itemSimples: $itemPedido);
                continue;
            }
        }
    }

    private function atualizarEstoqueItemPizza(Pedido $pedido, ItemPizza $itemPizza): void {
        $saboresEscolhidos = $itemPizza->saboresEscolhidos;
        $quantidadeSabores = count($saboresEscolhidos);

        foreach ($saboresEscolhidos as $saborEscolhido) {
            $ingredientesEscolhidos = $saborEscolhido->ingredientesEscolhidos;

            foreach ($ingredientesEscolhidos as $ingredienteEscolhido) {
                $consumo = $saborEscolhido->obterConsumo(
                    ingredienteEscolhido: $ingredienteEscolhido,
                    tamanhoPizza: $itemPizza->tamanhoPizza,
                    quantidadeSabores: $quantidadeSabores,
                    quantidadeItem: $itemPizza->quantidade,
                );

                $this->repositorioIngrediente->atualizarEstoqueIngrediente(
                    idIngrediente: $ingredienteEscolhido->ingrediente->id,
                    quantidade: $consumo,
                    tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
                );

                $this->repositorioMovimentacaoEstoque->registrarMovimentacaoEstoque(
                    pedido: $pedido,
                    movimentacao: new MovimentacaoEstoqueIngrediente(
                        id: 0,
                        data: new DateTime(),
                        tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
                        quantidade: $consumo,
                        ingrediente: $ingredienteEscolhido->ingrediente,
                    ),
                );
            }
        }
    }

    private function atualizarEstoqueItemSimples(Pedido $pedido, ItemSimples $itemSimples): void {
        $consumo = $itemSimples->obterConsumo();

        $this->repositorioProdutoSimples->atualizarEstoqueProdutoSimples(
            idProduto: $itemSimples->produtoSimples->id,
            quantidade: $consumo,
            tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
        );

        $this->repositorioMovimentacaoEstoque->registrarMovimentacaoEstoque(
            pedido: $pedido,
            movimentacao: new MovimentacaoEstoqueProdutoSimples(
                id: 0,
                data: new DateTime(),
                tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
                quantidade: $consumo,
                produtoSimples: $itemSimples->produtoSimples,
            ),
        );
    }

    private function marcarBeneficioFidelidadeComoUtilizado(Pedido $pedido): void {
        if (!$pedido->temDescontoFidelidade()) {
            return;
        }

        $this->repositorioCliente->marcarBeneficioFidelidadeComoUtilizado(
            cliente: $pedido->cliente,
            beneficioFidelidade: $pedido->descontoFidelidade->beneficioFidelidade,
        );
    }

    private function gerarNovoBeneficioFidelidade(Pedido $pedido): void {
        if (!$pedido->temCliente()) {
            return;
        }

        $configuracaoFidelidade = $this->repositorioConfiguracaoFidelidade->obterConfiguracaoFidelidade();
        if (!$configuracaoFidelidade instanceof ConfiguracaoFidelidade) {
            return;
        }

        $beneficioFidelidade = new GeradorBeneficioFidelidade()->gerarBeneficioFidelidade(
            pedido: $pedido,
            configuracaoFidelidade: $configuracaoFidelidade,
        );

        if (!$beneficioFidelidade instanceof BeneficioFidelidade) {
            return;
        }

        $this->repositorioCliente->registrarBeneficioFidelidade(cliente: $pedido->cliente, beneficioFidelidade: $beneficioFidelidade);
    }
}
