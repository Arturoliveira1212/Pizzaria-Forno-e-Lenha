<?php

class GestorPedido {
    public function __construct(
        private readonly UnidadeTrabalho $unidadeTrabalho,
        private readonly RepositorioPedido $repositorioPedido,
        private readonly RepositorioCliente $repositorioCliente,
        private readonly RepositorioPizza $repositorioPizza,
        private readonly RepositorioProdutoSimples $repositorioProdutoSimples,
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
        $tipoAtendimento = TipoAtendimento::tryFrom($pedidoParaCadastro->tipoAtendimento);
        if ($tipoAtendimento === null) {
            throw new DominioException("Tipo de atendimento inválido: {$pedidoParaCadastro->tipoAtendimento}.");
        }

        [$itensPizzaParaCadastro, $itensSimplesParaCadastro] = $pedidoParaCadastro->obterItensPorTipo();
        $itensPìzza = $this->criarItensPizza($itensPizzaParaCadastro);
        $itensSimples = $this->criarItensSimples($itensSimplesParaCadastro);

        $itensPedido = [...$itensPìzza, ...$itensSimples];

        $descontosPromocao = $this->criarDescontosPromocao($itensPìzza);

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
     * @param ItemPizzaParaCadastro[] $itensPizzaParaCadastro
     * @return ItemPizza[]
     */
    private function criarItensPizza(array $itensPizzaParaCadastro): array {
        $erros = [];
        $itensPìzza = [];

        foreach ($itensPizzaParaCadastro as $itemPizzaParaCadastro) {
            $tamanhoPizza = TamanhoPizza::tryFrom($itemPizzaParaCadastro->tamanho);
            if ($tamanhoPizza === null) {
                $erros[] = "Tamanho de pizza inválido: {$itemPizzaParaCadastro->tamanho}.";
                continue;
            }

            $saboresEscolhidos = $this->criarSaboresEscolhidos($itemPizzaParaCadastro->saboresEscolhidos);

            try {
                $itemPizza = new ItemPizza(
                    id: 0,
                    quantidade: $itemPizzaParaCadastro->quantidade,
                    saboresEscolhidos: $saboresEscolhidos,
                    tamanhoPizza: $tamanhoPizza,
                );
            } catch (DominioException $e) {
                $erros = [...$erros, ...$e->erros()];
                continue;
            }

            $itensPìzza[] = $itemPizza;
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        return $itensPìzza;
    }

    /**
     * @param SaborEscolhidoParaCadastro[] $saboresEscolhidosParaCadastro
     * @return SaborEscolhido[]
     */
    private function criarSaboresEscolhidos(array $saboresEscolhidosParaCadastro): array {
        $erros = [];
        $saboresEscolhidos = [];

        foreach ($saboresEscolhidosParaCadastro as $saborEscolhidoParaCadastro) {
            $pizza = $this->repositorioPizza->obterPizzaPorId($saborEscolhidoParaCadastro->idPizza);
            if (!$pizza instanceof Pizza) {
                $erros[] = "Pizza (ID {$saborEscolhidoParaCadastro->idPizza}) não encontrada.";
                continue;
            }

            $ingredientesEscolhidos = [];

            foreach ($pizza->ingredientesPadrao as $ingredientePadrao) {
                $ingredientesEscolhidos[] = new IngredienteEscolhido(
                    id: 0,
                    ingrediente: $ingredientePadrao->ingrediente,
                    papel: PapelIngrediente::PADRAO,
                    custoAdicional: Dinheiro::zero(),
                );
            }

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

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        return $saboresEscolhidos;
    }

    /**
     * @param ItemSimplesParaCadastro[] $itensSimplesParaCadastro
     * @return ItemSimples[]
     */
    private function criarItensSimples(array $itensSimplesParaCadastro): array {
        $erros = [];
        $itensSimples = [];

        foreach ($itensSimplesParaCadastro as $itemSimplesParaCadastro) {
            $produtoSimples = $this->repositorioProdutoSimples->obterProdutoSimplesPorId($itemSimplesParaCadastro->idProduto);
            if (!$produtoSimples instanceof ProdutoSimples) {
                $erros[] = "Produto simples (ID {$itemSimplesParaCadastro->idProduto}) não encontrado.";
                continue;
            }

            $itensSimples[] = new ItemSimples(
                id: 0,
                quantidade: $itemSimplesParaCadastro->quantidade,
                produtoSimples: $produtoSimples,
            );
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        return $itensSimples;
    }

    /**
     * @param ItemPizza[] $itensPizza
     * @return DescontoPromocao[]
     */
    private function criarDescontosPromocao(array $itensPizza): array {
        $promocoesVigentes = $this->repositorioPromocao->obterPromocoes(new FiltroPromocao(apenasVigentes: true));

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
