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

    public function registrarPedido(PedidoParaCadastro $pedido): Pedido {
        $pedido = $this->criarPedido($pedido);

        $this->unidadeTrabalho->executar(function () use ($pedido) {
            $this->repositorioPedido->registrarPedido($pedido);

            if ($pedido->temCliente()) {
                if ($pedido->temDescontoFidelidade()) {
                    $this->repositorioCliente->marcarBeneficioFidelidadeComoUtilizado(
                        cliente: $pedido->cliente,
                        beneficioFidelidade: $pedido->descontoFidelidade->beneficioFidelidade,
                    );
                }

                $configuracaoFidelidade = $this->repositorioConfiguracaoFidelidade->obterConfiguracaoFidelidade();
                if ($configuracaoFidelidade instanceof ConfiguracaoFidelidade) {
                    $beneficioFidelidade = $this->criarBeneficioFidelidadeParaCliente(cliente: $pedido->cliente, configuracaoFidelidade: $configuracaoFidelidade);
                    $this->repositorioCliente->registrarBeneficioFidelidade(cliente: $pedido->cliente, beneficioFidelidade: $beneficioFidelidade);
                }
            }
        });

        return $pedido;
    }

    private function criarPedido(PedidoParaCadastro $pedidoParaCadastro): Pedido {
        $tipoAtendimento = TipoAtendimento::tryFrom($pedidoParaCadastro->tipo);
        if ($tipoAtendimento === null) {
            throw new DominioException("Tipo de atendimento inválido: {$pedidoParaCadastro->tipo}.");
        }

        $itensPìzza = $this->criarItensPizza($pedidoParaCadastro->itensPizza);
        $itensSimples = $this->criarItensSimples($pedidoParaCadastro->itensSimples);

        $itensPedido = [...$itensPìzza, ...$itensSimples];

        $descontosPromocao = $this->criarDescontosPromocao($itensPìzza);

        $descontoFidelidade = null;
        $cliente = $this->repositorioCliente->obterClientePorId($pedidoParaCadastro->idCliente);
        if ($cliente instanceof Cliente && $cliente->temBeneficioFidelidade()) {
            $descontoFidelidade = $this->criarDescontoFidelidade(
                beneficioFidelidade: $cliente->beneficioFidelidade,
                itensPedido: $itensPedido,
                descontosPromocao: $descontosPromocao,
                erros: $erros,
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

            $ingredientesEscolhidos = $this->criarIngredientesEscolhidos(
                pizza: $pizza,
                ingredientesEscolhidosParaCadastro: $saborEscolhidoParaCadastro->ingredientesEscolhidos,
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

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        return $saboresEscolhidos;
    }

    /**
     * @param IngredienteEscolhidoParaCadastro[] $ingredientesEscolhidosParaCadastro
     * @return IngredienteEscolhido[]
     */
    private function criarIngredientesEscolhidos(Pizza $pizza, array $ingredientesEscolhidosParaCadastro): array {
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
                if ($adicional instanceof IngredienteAdicional) {
                    $custo = $adicional->preco;
                }
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

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        return $ingredientesEscolhidos;
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
        if (empty($promocoesVigentes)) {
            return [];
        }

        $valorTotalPorTamanhos = [];

        foreach ($itensPizza as $itemPizza) {
            $tamanho = $itemPizza->tamanhoPizza;
            if (!isset($valorTotalPorTamanhos[$tamanho->value])) {
                $valorTotalPorTamanhos[$tamanho->value] = [
                    'quantidade' => 0,
                    'valor' => Dinheiro::zero(),
                ];
            }

            $valorTotalPorTamanhos[$tamanho->value] = [
                'quantidade' => $valorTotalPorTamanhos[$tamanho->value]['quantidade'] + $itemPizza->quantidade,
                'valor' => $valorTotalPorTamanhos[$tamanho->value]['valor']->somar($itemPizza->calcularPrecoTotal()),
            ];
        }

        $descontosPromocao = [];

        foreach ($promocoesVigentes as $promocao) {
            $quantidadeTotal = $valorTotalPorTamanhos[$promocao->tamanhoPizza->value]['quantidade'] ?? 0;
            if ($quantidadeTotal < $promocao->quantidadeMinima) {
                continue;
            }

            $valorTotal = $valorTotalPorTamanhos[$promocao->tamanhoPizza->value]['valor'];
            $valorDesconto = $promocao->percentualDesconto->subtrairDe($valorTotal);

            $descontosPromocao[] = new DescontoPromocao(
                id: 0,
                promocao: $promocao,
                valorDesconto: $valorDesconto,
            );
        }

        return $descontosPromocao;
    }

    /**
     * @param ItemPedido[] $itensPedido
     * @param DescontoPromocao[] $descontosPromocao
     * @param string[] $erros
     */
    private function criarDescontoFidelidade(BeneficioFidelidade $beneficioFidelidade, array $itensPedido, array $descontosPromocao, array &$erros): ?DescontoFidelidade {
        if (!$beneficioFidelidade->estaValido()) {
            return null;
        }

        $valorTotal = Dinheiro::zero();

        foreach ($itensPedido as $itemPedido) {
            $valorTotal = $valorTotal->somar($itemPedido->calcularPrecoTotal());
        }

        $valorTotalDescontoPromocao = Dinheiro::zero();
        foreach ($descontosPromocao as $descontoPromocao) {
            $valorTotalDescontoPromocao = $valorTotalDescontoPromocao->somar($descontoPromocao->valorDesconto);
        }

        $valorTotal = $valorTotal->subtrair($valorTotalDescontoPromocao);
        $valorDesconto = $beneficioFidelidade->percentualDesconto->subtrairDe($valorTotal);

        return new DescontoFidelidade(
            id: 0,
            valorDesconto: $valorDesconto,
            beneficioFidelidade: $beneficioFidelidade,
        );
    }

    private function criarBeneficioFidelidadeParaCliente(Cliente $cliente, ConfiguracaoFidelidade $configuracaoFidelidade): BeneficioFidelidade {
        return new BeneficioFidelidade(
            id: 0,
            percentualDesconto: $configuracaoFidelidade->percentual,
            dataConcessao: new DateTime(),
            validade: (new DateTime())->add(new DateInterval("P{$configuracaoFidelidade->validadeEmDias}D")),
            utilizado: false,
        );
    }
}
