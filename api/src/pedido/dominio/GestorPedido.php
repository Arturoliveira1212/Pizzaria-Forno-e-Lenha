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
        private readonly ProvedorUsuarioLogado $provedorUsuarioLogado,
    ) {}

    /**
     * @return PedidoParaListagem[]
     */
    public function obterPedidosParaListagem(?int $idPedido = null): array {
        return $this->repositorioPedido->obterPedidosParaListagem($idPedido);
    }

    public function obterDetalhesPedido(int $idPedido): DetalhesPedido {
        return DetalhesPedido::criarComEntidade($this->obterPedidoPorId($idPedido));
    }

    public function alterarStatus(int $idPedido, string $novoStatus, string $motivoCancelamento = ''): void {
        $status = StatusPedido::tryFrom($novoStatus);
        if ($status === null) {
            throw new DominioException("Status de pedido inválido: {$novoStatus}.");
        }

        if ($status === StatusPedido::CANCELADO) {
            $this->cancelarPedido($idPedido, $motivoCancelamento);

            return;
        }

        if ($status === StatusPedido::SAIU_PARA_ENTREGA) {
            throw new DominioException('A saída para entrega do pedido exige um entregador.');
        }

        if ($status === StatusPedido::FINALIZADO) {
            $this->finalizarPedido($idPedido);

            return;
        }

        $this->repositorioPedido->alterarStatus($idPedido, $status);
    }

    public function registrarSaidaParaEntrega(SaidaParaEntregaParaCadastro $saidaParaEntregaParaCadastro): void {
        $pedido = $this->obterPedidoPorId($saidaParaEntregaParaCadastro->idPedido);
        if (!$pedido->cliente instanceof Cliente) {
            throw new DominioException('A entrega exige um cliente.');
        }

        $entregador = $this->obterEntregadorDisponivel($saidaParaEntregaParaCadastro->idEntregador);
        $entrega = new Entrega(
            id: 0,
            endereco: $pedido->cliente->endereco,
            entregador: $entregador,
            dataSaida: new DateTime(),
            dataChegada: null,
            taxa: $pedido->calcularValorEntrega(),
        );

        $this->unidadeTrabalho->executar(function () use ($pedido, $entrega) {
            $this->repositorioPedido->alterarStatus($pedido->id, StatusPedido::SAIU_PARA_ENTREGA);
            $this->repositorioPedido->registrarEntrega($pedido, $entrega);
        });
    }

    private function finalizarPedido(int $idPedido): void {
        $pedido = $this->obterPedidoPorId($idPedido);
        if (!$pedido->entrega instanceof Entrega) {
            $this->repositorioPedido->alterarStatus($idPedido, StatusPedido::FINALIZADO);

            return;
        }

        $entrega = $pedido->entrega->copiarComDataChegada(new DateTime());

        $this->unidadeTrabalho->executar(function () use ($idPedido, $entrega) {
            $this->repositorioPedido->alterarStatus($idPedido, StatusPedido::FINALIZADO);
            $this->repositorioPedido->registrarChegadaEntrega($entrega);
        });
    }

    private function obterPedidoPorId(int $idPedido): Pedido {
        $pedido = $this->repositorioPedido->obterPedidoPorId((string) $idPedido);
        if (!$pedido instanceof Pedido) {
            throw new NaoEncontradoException("Pedido (ID {$idPedido}) não encontrado.");
        }

        return $pedido;
    }

    private function obterEntregadorDisponivel(int $idEntregador): Entregador {
        $entregador = $this->repositorioEntregador->obterEntregadorPorId($idEntregador);
        if (!$entregador instanceof Entregador) {
            throw new DominioException("Entregador (ID {$idEntregador}) não encontrado.");
        }

        if (!$entregador->disponivel) {
            throw new DominioException("O entregador {$entregador->nome} (ID {$entregador->id}) não está disponível.");
        }

        return $entregador;
    }

    private function cancelarPedido(int $idPedido, string $motivoCancelamento): void {
        $cancelamento = new Cancelamento(
            id: 0,
            motivo: trim($motivoCancelamento),
            data: new DateTime(),
        );

        $pedido = $this->obterPedidoPorId($idPedido);

        $this->unidadeTrabalho->executar(function () use ($idPedido, $pedido, $cancelamento) {
            $this->repositorioPedido->alterarStatus($idPedido, StatusPedido::CANCELADO);
            $this->repositorioPedido->registrarCancelamento($idPedido, $cancelamento);
            $this->atualizarEstoqueAposCancelarPedido($pedido);
        });
    }

    public function registrarPedido(PedidoParaCadastro $pedidoParaCadastro): Pedido {
        $pedido = $this->criarPedido($pedidoParaCadastro);
        $this->validarEstoqueItensPedido($pedido->itensPedido);

        $this->unidadeTrabalho->executar(function () use (&$pedido) {
            $this->repositorioPedido->registrarPedido($pedido);
            $this->marcarBeneficioFidelidadeComoUtilizado($pedido);
            $this->gerarNovoBeneficioFidelidade($pedido);
            $this->atualizarEstoqueAposRegistrarPedido($pedido);
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

        return new Pedido(
            id: 0,
            usuario: $this->provedorUsuarioLogado->obterUsuarioLogado(),
            data: new DateTime(),
            tipoAtendimento: $tipoAtendimento,
            cliente: $cliente,
            itensPedido: $itensPedido,
            status: StatusPedido::inicial(),
            descontosPromocao: $descontosPromocao,
            descontoFidelidade: $descontoFidelidade,
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

    private function atualizarEstoqueIngrediente(Pedido $pedido, Ingrediente $ingrediente, QuantidadeMedida $consumo, TipoOperacaoEstoque $tipoOperacaoEstoque): void {
        $this->repositorioIngrediente->atualizarEstoqueIngrediente(
            idIngrediente: $ingrediente->id,
            quantidade: $consumo,
            tipoOperacaoEstoque: $tipoOperacaoEstoque,
        );

        $this->repositorioMovimentacaoEstoque->registrarMovimentacaoEstoque(
            pedido: $pedido,
            movimentacao: new MovimentacaoEstoqueIngrediente(
                id: 0,
                data: new DateTime(),
                tipoOperacaoEstoque: $tipoOperacaoEstoque,
                quantidade: $consumo,
                ingrediente: $ingrediente,
            ),
        );
    }

    private function atualizarEstoqueProdutoSimples(Pedido $pedido, ProdutoSimples $produtoSimples, QuantidadeMedida $consumo, TipoOperacaoEstoque $tipoOperacaoEstoque): void {
        $this->repositorioProdutoSimples->atualizarEstoqueProdutoSimples(
            idProduto: $produtoSimples->id,
            quantidade: $consumo,
            tipoOperacaoEstoque: $tipoOperacaoEstoque,
        );

        $this->repositorioMovimentacaoEstoque->registrarMovimentacaoEstoque(
            pedido: $pedido,
            movimentacao: new MovimentacaoEstoqueProdutoSimples(
                id: 0,
                data: new DateTime(),
                tipoOperacaoEstoque: $tipoOperacaoEstoque,
                quantidade: $consumo,
                produtoSimples: $produtoSimples,
            ),
        );
    }

    private function atualizarEstoqueAposRegistrarPedido(Pedido $pedido): void {
        foreach ($pedido->itensPedido as $itemPedido) {
            if ($itemPedido instanceof ItemPizza) {
                $this->atualizarEstoqueItemPizzaAposRegistrarPedido(pedido: $pedido, itemPizza: $itemPedido);
                continue;
            }

            if ($itemPedido instanceof ItemSimples) {
                $this->atualizarEstoqueItemSimplesAposRegistrarPedido(pedido: $pedido, itemSimples: $itemPedido);
                continue;
            }
        }
    }

    private function atualizarEstoqueItemPizzaAposRegistrarPedido(Pedido $pedido, ItemPizza $itemPizza): void {
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

                $this->atualizarEstoqueIngrediente(
                    pedido: $pedido,
                    ingrediente: $ingredienteEscolhido->ingrediente,
                    consumo: $consumo,
                    tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
                );
            }
        }
    }

    private function atualizarEstoqueItemSimplesAposRegistrarPedido(Pedido $pedido, ItemSimples $itemSimples): void {
        $consumo = $itemSimples->obterConsumo();

        $this->atualizarEstoqueProdutoSimples(
            pedido: $pedido,
            produtoSimples: $itemSimples->produtoSimples,
            consumo: $consumo,
            tipoOperacaoEstoque: TipoOperacaoEstoque::SAIDA,
        );
    }

    private function atualizarEstoqueAposCancelarPedido(Pedido $pedido): void {
        $movimentacoesSaida = $pedido->obterMovimentacoesEstoque(TipoOperacaoEstoque::SAIDA);

        foreach ($movimentacoesSaida as $movimentacaoSaida) {
            if ($movimentacaoSaida instanceof MovimentacaoEstoqueIngrediente) {
                $this->atualizarEstoqueIngredienteAposCancelarPedido($pedido, $movimentacaoSaida);
            }

            if ($movimentacaoSaida instanceof MovimentacaoEstoqueProdutoSimples) {
                $this->atualizarEstoqueProdutoSimplesAposCancelarPedido($pedido, $movimentacaoSaida);
            }
        }
    }

    private function atualizarEstoqueIngredienteAposCancelarPedido(Pedido $pedido, MovimentacaoEstoqueIngrediente $movimentacaoSaida): void {
        $ingrediente = $movimentacaoSaida->ingrediente;
        $consumo = $movimentacaoSaida->quantidadeMedida;

        $this->atualizarEstoqueIngrediente(
            pedido: $pedido,
            ingrediente: $ingrediente,
            consumo: $consumo,
            tipoOperacaoEstoque: TipoOperacaoEstoque::ENTRADA,
        );
    }

    private function atualizarEstoqueProdutoSimplesAposCancelarPedido(Pedido $pedido, MovimentacaoEstoqueProdutoSimples $movimentacaoSaida): void {
        $produtoSimples = $movimentacaoSaida->produtoSimples;
        $consumo = $movimentacaoSaida->quantidadeMedida;

        $this->atualizarEstoqueProdutoSimples(
            pedido: $pedido,
            produtoSimples: $produtoSimples,
            consumo: $consumo,
            tipoOperacaoEstoque: TipoOperacaoEstoque::ENTRADA,
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
