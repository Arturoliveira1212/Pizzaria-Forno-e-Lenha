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
    ) {}

    /**
     * @return PedidoParaListagem[]
     */
    public function obterPedidos(?int $idPedido = null): array {
        return $this->repositorioPedido->obterPedidos($idPedido);
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

        $this->unidadeTrabalho->executar(function () use ($pedido) {
            $this->repositorioPedido->registrarPedido($pedido);
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
        $itensPedido = [];

        try {
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
                usuario: UsuarioFactory::atendente(), // TODO: Temporário para passar os testes
                data: new DateTime(),
                tipoAtendimento: $tipoAtendimento,
                cliente: $cliente,
                itensPedido: $itensPedido,
                status: StatusPedido::inicial(),
                descontosPromocao: $descontosPromocao,
                descontoFidelidade: $descontoFidelidade,
            );
        } catch (DominioException $e) {
            throw new RegistroPedidoException(
                erros: $e->erros(),
                itensPedidoAtualizados: $itensPedido,
            );
        }
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

        $validador = new ValidadorEstoqueItemPedido();

        if (!empty($itensSimples)) {
            $itensSimples = $validador->validarEstoqueItensSimples(itensSimples: $itensSimples, erros: $erros);
        }

        if (!empty($itensPizza)) {
            $itensPizza = $validador->validarEstoqueItensPizza(itensPizza: $itensPizza, erros: $erros);
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
