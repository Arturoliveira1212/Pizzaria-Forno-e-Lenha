<?php

class RepositorioPedidoEmBDR implements RepositorioPedido {
    public function __construct(
        private readonly BancoDados $bancoDados,
        private readonly RepositorioUsuario $repositorioUsuario,
        private readonly RepositorioCliente $repositorioCliente,
        private readonly RepositorioPizza $repositorioPizza,
        private readonly RepositorioEntregador $repositorioEntregador,
    ) {}

    public function obterPedidosParaListagem(?int $idPedido = null): array {
        try {
            $parametros = [];
            $sql = 'SELECT
                pedido.id,
                pedido.data,
                pedido.valorTotal,
                pedido.tipoAtendimento,
                pedido.status,
                cliente.nome AS nomeCliente
                    FROM pedido
                        LEFT JOIN cliente ON cliente.id = pedido.idCliente
                            WHERE 1=1';

            if ($idPedido !== null) {
                $sql .= ' AND pedido.id = :id';
                $parametros['id'] = $idPedido;
            }

            $sql .= ' ORDER BY pedido.id DESC LIMIT 50';

            $resultado = $this->bancoDados->consultar($sql, $parametros);
            if (empty($resultado)) {
                return [];
            }

            $pedidos = [];
            foreach ($resultado as $linha) {
                $tipoAtendimento = TipoAtendimento::from($linha['tipoAtendimento']);
                $status = StatusPedido::from($linha['status']);
                $pedidos[] = new PedidoParaListagem(
                    id: (int) $linha['id'],
                    data: $linha['data'],
                    nomeCliente: $linha['nomeCliente'],
                    tipoAtendimento: $tipoAtendimento->value,
                    status: $status->value,
                    proximosStatus: array_map(
                        fn(StatusPedido $status): string => $status->value,
                        $status->proximos($tipoAtendimento),
                    ),
                    valorTotalCentavos: (int) $linha['valorTotal'],
                );
            }

            return $pedidos;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pedidos.');
        }
    }

    public function alterarStatus(int $idPedido, StatusPedido $novoStatus): void {
        try {
            $resultado = $this->bancoDados->consultar(
                'SELECT status, tipoAtendimento FROM pedido WHERE id = :id',
                ['id' => $idPedido],
            );
            if (empty($resultado)) {
                throw new DominioException("Pedido (ID {$idPedido}) não encontrado.");
            }

            $linha = $resultado[0];
            $statusAtual = StatusPedido::from($linha['status']);
            $tipoAtendimento = TipoAtendimento::from($linha['tipoAtendimento']);

            if (!in_array($novoStatus, $statusAtual->proximos($tipoAtendimento), true)) {
                throw new DominioException("Não é possível alterar o pedido de {$statusAtual->value} para {$novoStatus->value}.");
            }

            $this->bancoDados->executar(
                'UPDATE pedido SET status = :status WHERE id = :id',
                ['status' => $novoStatus->value, 'id' => $idPedido],
            );
        } catch (DominioException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao alterar status do pedido.');
        }
    }

    public function registrarCancelamento(int $idPedido, Cancelamento $cancelamento): void {
        try {
            $sql = 'INSERT INTO cancelamento (
                    idPedido,
                    motivo,
                    data
                ) VALUES (
                    :pedidoId,
                    :motivo,
                    :data
                )';

            $this->bancoDados->executar($sql, [
                'pedidoId' => $idPedido,
                'motivo' => $cancelamento->motivo,
                'data' => $cancelamento->data->format('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar cancelamento do pedido.');
        }
    }

    public function registrarEntrega(Pedido $pedido, Entrega $entrega): void {
        try {
            $sql = 'INSERT INTO entrega (
                    idPedido,
                    dataSaida,
                    dataChegada,
                    taxa,
                    idEndereco,
                    idEntregador
                ) VALUES (
                    :pedidoId,
                    :dataSaida,
                    :dataChegada,
                    :taxa,
                    :enderecoId,
                    :entregadorId
                )';

            $this->bancoDados->executar($sql, [
                'pedidoId' => $pedido->id,
                'dataSaida' => $entrega->dataSaida?->format('Y-m-d H:i:s'),
                'dataChegada' => $entrega->dataChegada?->format('Y-m-d H:i:s'),
                'taxa' => $entrega->taxa->centavos,
                'enderecoId' => $entrega->endereco->id,
                'entregadorId' => $entrega->entregador->id,
            ]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar entrega do pedido.');
        }
    }

    public function registrarChegadaEntrega(Entrega $entrega): void {
        try {
            $sql = 'UPDATE entrega SET
                dataChegada = :dataChegada
                    WHERE id = :id';

            $this->bancoDados->executar($sql, [
                'dataChegada' => $entrega->dataChegada?->format('Y-m-d H:i:s'),
                'id' => $entrega->id,
            ]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar chegada da entrega do pedido.');
        }
    }

    public function registrarPedido(Pedido &$pedido): void {
        try {
            $this->salvarPedido($pedido);
            $this->salvarItensPedido($pedido);
            $this->salvarDescontos($pedido);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao registrar pedido.');
        }
    }

    private function salvarPedido(Pedido &$pedido): void {
        $sql = 'INSERT INTO pedido (
                idUsuario,
                data,
                valorTotal,
                taxaEntrega,
                status,
                tipoAtendimento,
                idCliente
            ) VALUES (
                :idUsuario,
                :data,
                :valorTotal,
                :taxaEntrega,
                :status,
                :tipoAtendimento,
                :idCliente
            )';

        $this->bancoDados->executar($sql, [
            'idUsuario' => $pedido->usuario->id,
            'data' => $pedido->data->format('Y-m-d H:i:s'),
            'valorTotal' => $pedido->calcularValorTotal()->centavos,
            'taxaEntrega' => $pedido->calcularValorEntrega()->centavos,
            'status' => $pedido->status->value,
            'tipoAtendimento' => $pedido->tipoAtendimento->value,
            'idCliente' => $pedido->cliente?->id,
        ]);

        $ultimoIdInserido = (int) $this->bancoDados->ultimoIdInserido();
        $pedido = $pedido->copiarComId($ultimoIdInserido);
    }

    private function salvarDescontos(Pedido $pedido): void {
        foreach ($pedido->descontosPromocao as $desconto) {
            $sql = 'INSERT INTO desconto_promocao (
                    idPedido,
                    valorDesconto,
                    idPromocao
                ) VALUES (
                    :pedidoId,
                    :valorDesconto,
                    :promocaoId
                )';

            $this->bancoDados->executar($sql, [
                'pedidoId' => $pedido->id,
                'valorDesconto' => $desconto->valorDesconto->centavos,
                'promocaoId' => $desconto->promocao->id,
            ]);
        }

        if ($pedido->descontoFidelidade instanceof DescontoFidelidade) {
            $sql = 'INSERT INTO desconto_fidelidade (
                    idPedido,
                    valorDesconto,
                    idBeneficioFidelidade
                ) VALUES (
                    :pedidoId,
                    :valorDesconto,
                    :beneficioFidelidadeId
                )';

            $this->bancoDados->executar($sql, [
                'pedidoId' => $pedido->id,
                'valorDesconto' => $pedido->descontoFidelidade->valorDesconto->centavos,
                'beneficioFidelidadeId' => $pedido->descontoFidelidade->beneficioFidelidade->id,
            ]);
        }
    }

    private function salvarItensPedido(Pedido $pedido): void {
        foreach ($pedido->itensPedido as $itemPedido) {
            if ($itemPedido instanceof ItemPizza) {
                $this->salvarItemPizza($pedido->id, $itemPedido);
                continue;
            }

            if ($itemPedido instanceof ItemSimples) {
                $this->salvarItemSimples($pedido->id, $itemPedido);
            }
        }
    }

    private function salvarItemSimples(int $idPedido, ItemSimples $itemSimples): void {
        $sql = 'INSERT INTO item_pedido_simples (
                idPedido,
                quantidade,
                precoPraticado,
                idProdutoSimples
            ) VALUES (
                :pedidoId,
                :quantidade,
                :precoPraticado,
                :produtoSimplesId
            )';

        $this->bancoDados->executar($sql, [
            'pedidoId' => $idPedido,
            'quantidade' => $itemSimples->quantidade,
            'precoPraticado' => $itemSimples->precoPraticado->centavos,
            'produtoSimplesId' => $itemSimples->produtoSimples->id,
        ]);
    }

    private function salvarItemPizza(int $idPedido, ItemPizza $itemPizza): void {
        $sql = 'INSERT INTO item_pedido_pizza (
                idPedido,
                quantidade,
                precoPraticado,
                tamanho
            ) VALUES (
                :pedidoId,
                :quantidade,
                :precoPraticado,
                :tamanho
            )';

        $this->bancoDados->executar($sql, [
            'pedidoId' => $idPedido,
            'quantidade' => $itemPizza->quantidade,
            'precoPraticado' => $itemPizza->precoPraticado->centavos,
            'tamanho' => $itemPizza->tamanhoPizza->value,
        ]);

        $idItemPizza = (int) $this->bancoDados->ultimoIdInserido();
        foreach ($itemPizza->saboresEscolhidos as $saborEscolhido) {
            $this->salvarSaborEscolhido($idItemPizza, $saborEscolhido);
        }
    }

    private function salvarSaborEscolhido(int $idItemPizza, SaborEscolhido $saborEscolhido): void {
        $sql = 'INSERT INTO sabor_escolhido (
                idItemPedidoPizza,
                idPizza
            ) VALUES (
                :itemPedidoPizzaId,
                :pizzaId
            )';

        $this->bancoDados->executar($sql, [
            'itemPedidoPizzaId' => $idItemPizza,
            'pizzaId' => $saborEscolhido->pizza->id,
        ]);

        $idSaborEscolhido = (int) $this->bancoDados->ultimoIdInserido();
        foreach ($saborEscolhido->ingredientesEscolhidos as $ingredienteEscolhido) {
            $this->salvarIngredienteEscolhido($idSaborEscolhido, $ingredienteEscolhido);
        }
    }

    private function salvarIngredienteEscolhido(int $idSaborEscolhido, IngredienteEscolhido $ingredienteEscolhido): void {
        $sql = 'INSERT INTO ingrediente_escolhido (
                idSaborEscolhido,
                idIngrediente,
                custo,
                papel
            ) VALUES (
                :saborEscolhidoId,
                :ingredienteId,
                :custo,
                :papel
            )';

        $this->bancoDados->executar($sql, [
            'saborEscolhidoId' => $idSaborEscolhido,
            'ingredienteId' => $ingredienteEscolhido->ingrediente->id,
            'custo' => $ingredienteEscolhido->custoAdicional->centavos,
            'papel' => $ingredienteEscolhido->papel->value,
        ]);
    }

    public function obterPedidoPorId(string $idPedido): ?Pedido {
        try {
            $sql = 'SELECT
                pedido.id,
                pedido.idUsuario,
                pedido.idCliente,
                pedido.data,
                pedido.taxaEntrega,
                pedido.status,
                pedido.tipoAtendimento
                    FROM pedido
                    WHERE pedido.id = :id';

            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $idPedido,
            ]);
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];
            $idPedido = (int) $linha['id'];
            $idsPedidos = [$idPedido];

            $usuario = $this->repositorioUsuario->obterUsuarioPorId((int) $linha['idUsuario']);

            $cliente = $linha['idCliente'] !== null
                ? $this->repositorioCliente->obterClientePorId((string) $linha['idCliente'])
                : null;

            $itensPedido = $this->buscarItensPedido($idsPedidos);
            $descontosPromocao = $this->buscarDescontosPromocao($idsPedidos);
            $descontosFidelidade = $this->buscarDescontoFidelidade($idsPedidos);
            $entregas = $this->buscarEntrega($idsPedidos);
            $cancelamentos = $this->buscarCancelamento($idsPedidos);
            $movimentacoesEstoque = $this->buscarMovimentacoesEstoque($idsPedidos);

            return new Pedido(
                id: (int) $linha['id'],
                usuario: $usuario,
                data: new DateTime($linha['data']),
                tipoAtendimento: TipoAtendimento::from($linha['tipoAtendimento']),
                cliente: $cliente,
                itensPedido: $itensPedido[$idPedido] ?? [],
                status: StatusPedido::from($linha['status']),
                descontosPromocao: $descontosPromocao[$idPedido] ?? [],
                descontoFidelidade: $descontosFidelidade[$idPedido][0] ?? null,
                entrega: $entregas[$idPedido][0] ?? null,
                cancelamento: $cancelamentos[$idPedido][0] ?? null,
                taxaEntrega: new Dinheiro((int) $linha['taxaEntrega']),
                movimentacoesEstoque: $movimentacoesEstoque[$idPedido] ?? [],
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pedido por ID.');
        }
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, ItemPedido[]>
     */
    private function buscarItensPedido(array $idsPedidos): array {
        $itensPizza = $this->buscarItensPizza($idsPedidos);
        $itensSimples = $this->buscarItensSimples($idsPedidos);

        $itensPedidos = [];

        foreach ($idsPedidos as $idPedido) {
            $itensPizzaDoPedido = $itensPizza[$idPedido] ?? [];
            $itensSimplesDoPedido = $itensSimples[$idPedido] ?? [];

            $itensPedidos[$idPedido] = [
                ...$itensPizzaDoPedido,
                ...$itensSimplesDoPedido,
            ];
        }

        return $itensPedidos;
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, ItemPizza[]>
     */
    private function buscarItensPizza(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            item_pedido_pizza.id,
            item_pedido_pizza.idPedido,
            item_pedido_pizza.quantidade,
            item_pedido_pizza.precoPraticado,
            item_pedido_pizza.tamanho
                FROM item_pedido_pizza
                    WHERE item_pedido_pizza.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        $idsItens = array_map('intval', array_column($resultado, 'id'));
        $sabores = $this->buscarSaboresEscolhidos($idsItens);

        return $this->agruparPorPedido($resultado, $this->criarItemPizza(...), $sabores);
    }

    /**
     * @param array<string, mixed> $linha
     * @param array<int, SaborEscolhido[]> $sabores
     */
    private function criarItemPizza(array $linha, array $sabores): ItemPizza {
        $idItem = (int) $linha['id'];

        return new ItemPizza(
            id: $idItem,
            quantidade: (int) $linha['quantidade'],
            saboresEscolhidos: $sabores[$idItem] ?? [],
            tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
            precoPraticado: new Dinheiro((int) $linha['precoPraticado']),
        );
    }

    /**
     * @param int[] $idsItens
     * @return array<int, SaborEscolhido[]>
     */
    private function buscarSaboresEscolhidos(array $idsItens): array {
        $idsItensString = implode(',', $idsItens);
        $sql = "SELECT
            sabor_escolhido.id,
            sabor_escolhido.idItemPedidoPizza,
            sabor_escolhido.idPizza
                FROM sabor_escolhido
                    WHERE sabor_escolhido.idItemPedidoPizza IN ({$idsItensString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        $idsSabores = array_map('intval', array_column($resultado, 'id'));
        $idsPizzas = array_map('intval', array_column($resultado, 'idPizza'));

        $ingredientesEscolhidos = $this->buscarIngredientesEscolhidos($idsSabores);
        $pizzas = $this->repositorioPizza->obterPizzasPorIds($idsPizzas);

        return $this->agruparPorItemPizza($resultado, $this->criarSaborEscolhido(...), $pizzas, $ingredientesEscolhidos);
    }

    /**
     * @param array<string, mixed> $linha
     * @param array<int, Pizza> $pizzas
     * @param array<int, IngredienteEscolhido[]> $ingredientesEscolhidos
     */
    private function criarSaborEscolhido(array $linha, array $pizzas, array $ingredientesEscolhidos): SaborEscolhido {
        $idSabor = (int) $linha['id'];

        return new SaborEscolhido(
            id: $idSabor,
            pizza: $pizzas[(int) $linha['idPizza']],
            ingredientesEscolhidos: $ingredientesEscolhidos[$idSabor] ?? [],
        );
    }

    /**
     * @param int[] $idsSabores
     * @return array<int, IngredienteEscolhido[]>
     */
    private function buscarIngredientesEscolhidos(array $idsSabores): array {
        $idsSaboresString = implode(',', $idsSabores);
        $sql = "SELECT
            ingrediente_escolhido.id,
            ingrediente_escolhido.idSaborEscolhido,
            ingrediente_escolhido.papel,
            ingrediente_escolhido.custo,
            ingrediente.id AS idIngrediente,
            ingrediente.nome,
            ingrediente.estoque,
            ingrediente.idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM ingrediente_escolhido
                    INNER JOIN ingrediente ON ingrediente.id = ingrediente_escolhido.idIngrediente
                    INNER JOIN unidade_medida ON unidade_medida.id = ingrediente.idUnidadeMedida
                        WHERE ingrediente_escolhido.idSaborEscolhido IN ({$idsSaboresString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorSaborEscolhido($resultado, $this->criarIngredienteEscolhido(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarIngredienteEscolhido(array $linha): IngredienteEscolhido {
        return new IngredienteEscolhido(
            id: (int) $linha['id'],
            ingrediente: new Ingrediente(
                id: (int) $linha['idIngrediente'],
                nome: $linha['nome'],
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedida'],
                        descricao: $linha['descricao'],
                        sigla: $linha['sigla'],
                    ),
                ),
            ),
            papel: PapelIngrediente::from($linha['papel']),
            custoAdicional: new Dinheiro((int) $linha['custo']),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, ItemSimples[]>
     */
    private function buscarItensSimples(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            item_pedido_simples.id,
            item_pedido_simples.idPedido,
            item_pedido_simples.quantidade,
            item_pedido_simples.precoPraticado,
            produto_simples.id AS idProdutoSimples,
            produto_simples.nome,
            produto_simples.descricao,
            produto_simples.tipo,
            produto_simples.preco,
            produto_simples.estoque,
            produto_simples.idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM item_pedido_simples
                    INNER JOIN produto_simples ON produto_simples.id = item_pedido_simples.idProdutoSimples
                    INNER JOIN unidade_medida ON unidade_medida.id = produto_simples.idUnidadeMedida
                        WHERE item_pedido_simples.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarItemSimples(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarItemSimples(array $linha): ItemSimples {
        return new ItemSimples(
            id: (int) $linha['id'],
            quantidade: (int) $linha['quantidade'],
            produtoSimples: new ProdutoSimples(
                id: (int) $linha['id'],
                nome: $linha['nome'],
                descricao: $linha['descricao'],
                tipo: TipoProdutoSimples::from($linha['tipo']),
                preco: new Dinheiro((int) $linha['preco']),
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedida'],
                        descricao: $linha['descricao'],
                        sigla: $linha['sigla'],
                    ),
                ),
            ),
            precoPraticado: new Dinheiro((int) $linha['precoPraticado']),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, DescontoPromocao[]>
     */
    private function buscarDescontosPromocao(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            desconto_promocao.id,
            desconto_promocao.idPedido,
            desconto_promocao.valorDesconto,
            promocao.id AS idPromocao,
            promocao.descricao,
            promocao.quantidadeMinima,
            promocao.tamanho,
            promocao.percentualDesconto,
            promocao.dataInicio,
            promocao.validade
                FROM desconto_promocao
                    INNER JOIN promocao ON promocao.id = desconto_promocao.idPromocao
                        WHERE desconto_promocao.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarDescontoPromocao(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarDescontoPromocao(array $linha): DescontoPromocao {
        return new DescontoPromocao(
            id: (int) $linha['id'],
            valorDesconto: new Dinheiro((int) $linha['valorDesconto']),
            promocao: new Promocao(
                id: (int) $linha['idPromocao'],
                descricao: $linha['descricao'],
                quantidadeMinima: (int) $linha['quantidadeMinima'],
                tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
                percentualDesconto: new Percentual((int) $linha['percentualDesconto']),
                dataInicio: $linha['dataInicio'] ? new DateTime($linha['dataInicio']) : null,
                dataFim: $linha['validade'] ? new DateTime($linha['validade']) : null,
            ),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, DescontoFidelidade[]>
     */
    private function buscarDescontoFidelidade(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            desconto_fidelidade.id,
            desconto_fidelidade.idPedido,
            desconto_fidelidade.valorDesconto,
            beneficio_fidelidade.id AS idBeneficioFidelidade,
            beneficio_fidelidade.idCliente,
            beneficio_fidelidade.percentualDesconto,
            beneficio_fidelidade.dataConcessao,
            beneficio_fidelidade.validade,
            beneficio_fidelidade.utilizado
                FROM desconto_fidelidade
                    INNER JOIN beneficio_fidelidade ON beneficio_fidelidade.id = desconto_fidelidade.idBeneficioFidelidade
                        WHERE desconto_fidelidade.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarDescontoFidelidade(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarDescontoFidelidade(array $linha): DescontoFidelidade {
        return new DescontoFidelidade(
            id: (int) $linha['id'],
            valorDesconto: new Dinheiro((int) $linha['valorDesconto']),
            beneficioFidelidade: new BeneficioFidelidade(
                id: (int) $linha['idBeneficioFidelidade'],
                percentualDesconto: new Percentual((int) $linha['percentualDesconto']),
                dataConcessao: new DateTime($linha['dataConcessao']),
                validade: new DateTime($linha['validade']),
                utilizado: (bool) $linha['utilizado'],
            ),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, Entrega[]>
     */
    private function buscarEntrega(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            entrega.id,
            entrega.idPedido,
            entrega.idEntregador,
            entrega.dataSaida,
            entrega.dataChegada,
            entrega.taxa,
            endereco.id AS idEndereco,
            endereco.logradouro,
            endereco.numero,
            endereco.bairro,
            endereco.cidade,
            endereco.estado,
            endereco.uf
                FROM entrega
                    INNER JOIN endereco ON endereco.id = entrega.idEndereco
                        WHERE entrega.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarEntrega(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarEntrega(array $linha): Entrega {
        return new Entrega(
            id: (int) $linha['id'],
            endereco: new Endereco(
                id: (int) $linha['idEndereco'],
                logradouro: $linha['logradouro'],
                numero: $linha['numero'],
                bairro: $linha['bairro'],
                cidade: $linha['cidade'],
                estado: $linha['estado'],
                uf: $linha['uf'],
            ),
            entregador: $this->repositorioEntregador->obterEntregadorPorId((int) $linha['idEntregador']),
            dataSaida: $linha['dataSaida'] !== null ? new DateTime($linha['dataSaida']) : null,
            dataChegada: $linha['dataChegada'] !== null ? new DateTime($linha['dataChegada']) : null,
            taxa: new Dinheiro((int) $linha['taxa']),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, Cancelamento[]>
     */
    private function buscarCancelamento(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            cancelamento.id,
            cancelamento.idPedido,
            cancelamento.motivo,
            cancelamento.data
                FROM cancelamento
                    WHERE cancelamento.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarCancelamento(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarCancelamento(array $linha): Cancelamento {
        return new Cancelamento(
            id: (int) $linha['id'],
            motivo: $linha['motivo'],
            data: new DateTime($linha['data']),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, MovimentacaoEstoque[]>
     */
    private function buscarMovimentacoesEstoque(array $idsPedidos): array {
        $movimentacoesEstoqueIngredientes = $this->buscarMovimentacoesEstoqueIngredientes($idsPedidos);
        $movimentacoesEstoqueProdutosSimples = $this->buscarMovimentacoesEstoqueProdutosSimples($idsPedidos);

        $movimentacoesEstoque = [];

        foreach ($idsPedidos as $idPedido) {
            $movimentacoesEstoqueIngredientesDoPedido = $movimentacoesEstoqueIngredientes[$idPedido] ?? [];
            $movimentacoesEstoqueProdutosSimplesDoPedido = $movimentacoesEstoqueProdutosSimples[$idPedido] ?? [];

            $movimentacoesEstoque[$idPedido] = [
                ...$movimentacoesEstoqueIngredientesDoPedido,
                ...$movimentacoesEstoqueProdutosSimplesDoPedido,
            ];
        }

        return $movimentacoesEstoque;
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, MovimentacaoEstoqueIngrediente[]>
     */
    private function buscarMovimentacoesEstoqueIngredientes(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            movimentacao_estoque_ingrediente.id,
            movimentacao_estoque_ingrediente.data,
            movimentacao_estoque_ingrediente.idPedido,
            movimentacao_estoque_ingrediente.quantidade,
            movimentacao_estoque_ingrediente.tipoOperacaoEstoque,
            ingrediente.id AS idIngrediente,
            ingrediente.nome,
            ingrediente.estoque,
            unidade_medida.id AS idUnidadeMedidaMovimentacao,
            unidade_medida.descricao AS descricaoUnidadeMedidaMovimentacao,
            unidade_medida.sigla AS siglaUnidadeMedidaMovimentacao,
            unidade_medida_ingrediente.id AS idUnidadeMedidaIngrediente,
            unidade_medida_ingrediente.descricao AS descricaoUnidadeMedidaIngrediente,
            unidade_medida_ingrediente.sigla AS siglaUnidadeMedidaIngrediente
                FROM movimentacao_estoque_ingrediente
                    INNER JOIN ingrediente ON ingrediente.id = movimentacao_estoque_ingrediente.idIngrediente
                    INNER JOIN unidade_medida ON unidade_medida.id = movimentacao_estoque_ingrediente.idUnidadeMedida
                    INNER JOIN unidade_medida unidade_medida_ingrediente ON unidade_medida_ingrediente.id = ingrediente.idUnidadeMedida
                WHERE movimentacao_estoque_ingrediente.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarMovimentacaoEstoqueIngrediente(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarMovimentacaoEstoqueIngrediente(array $linha): MovimentacaoEstoqueIngrediente {
        return new MovimentacaoEstoqueIngrediente(
            id: (int) $linha['id'],
            data: new DateTime($linha['data']),
            tipoOperacaoEstoque: TipoOperacaoEstoque::from($linha['tipoOperacaoEstoque']),
            quantidade: new QuantidadeMedida(
                valor: (int) $linha['quantidade'],
                unidadeMedida: new UnidadeMedida(
                    id: (int) $linha['idUnidadeMedidaMovimentacao'],
                    descricao: $linha['descricaoUnidadeMedidaMovimentacao'],
                    sigla: $linha['siglaUnidadeMedidaMovimentacao'],
                ),
            ),
            ingrediente: new Ingrediente(
                id: (int) $linha['idIngrediente'],
                nome: $linha['nome'],
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedidaIngrediente'],
                        descricao: $linha['descricaoUnidadeMedidaIngrediente'],
                        sigla: $linha['siglaUnidadeMedidaIngrediente'],
                    ),
                ),
            ),
        );
    }

    /**
     * @param int[] $idsPedidos
     * @return array<int, MovimentacaoEstoqueProdutoSimples[]>
     */
    private function buscarMovimentacoesEstoqueProdutosSimples(array $idsPedidos): array {
        $idsPedidosString = implode(',', $idsPedidos);
        $sql = "SELECT
            movimentacao_estoque_produto_simples.id,
            movimentacao_estoque_produto_simples.data,
            movimentacao_estoque_produto_simples.idPedido,
            movimentacao_estoque_produto_simples.quantidade,
            movimentacao_estoque_produto_simples.tipoOperacaoEstoque,
            produto_simples.id AS idProdutoSimples,
            produto_simples.nome,
            produto_simples.descricao,
            produto_simples.tipo,
            produto_simples.preco,
            produto_simples.estoque,
            unidade_medida.id AS idUnidadeMedidaMovimentacao,
            unidade_medida.descricao AS descricaoUnidadeMedidaMovimentacao,
            unidade_medida.sigla AS siglaUnidadeMedidaMovimentacao,
            unidade_medida_produto.id AS idUnidadeMedidaProduto,
            unidade_medida_produto.descricao AS descricaoUnidadeMedidaProduto,
            unidade_medida_produto.sigla AS siglaUnidadeMedidaProduto
                FROM movimentacao_estoque_produto_simples
                    INNER JOIN produto_simples ON produto_simples.id = movimentacao_estoque_produto_simples.idProdutoSimples
                    INNER JOIN unidade_medida ON unidade_medida.id = movimentacao_estoque_produto_simples.idUnidadeMedida
                    INNER JOIN unidade_medida unidade_medida_produto ON unidade_medida_produto.id = produto_simples.idUnidadeMedida
                WHERE movimentacao_estoque_produto_simples.idPedido IN ({$idsPedidosString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        return $this->agruparPorPedido($resultado, $this->criarMovimentacaoEstoqueProdutoSimples(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarMovimentacaoEstoqueProdutoSimples(array $linha): MovimentacaoEstoqueProdutoSimples {
        return new MovimentacaoEstoqueProdutoSimples(
            id: (int) $linha['id'],
            data: new DateTime($linha['data']),
            quantidade: new QuantidadeMedida(
                valor: (int) $linha['quantidade'],
                unidadeMedida: new UnidadeMedida(
                    id: (int) $linha['idUnidadeMedidaMovimentacao'],
                    descricao: $linha['descricaoUnidadeMedidaMovimentacao'],
                    sigla: $linha['siglaUnidadeMedidaMovimentacao'],
                ),
            ),
            tipoOperacaoEstoque: TipoOperacaoEstoque::from($linha['tipoOperacaoEstoque']),
            produtoSimples: new ProdutoSimples(
                id: (int) $linha['idProdutoSimples'],
                nome: $linha['nome'],
                descricao: $linha['descricao'],
                tipo: TipoProdutoSimples::from($linha['tipo']),
                preco: new Dinheiro((int) $linha['preco']),
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedidaProduto'],
                        descricao: $linha['descricaoUnidadeMedidaProduto'],
                        sigla: $linha['siglaUnidadeMedidaProduto'],
                    ),
                ),
            ),
        );
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorPedido(array $linhas, callable $criarObjeto, mixed ...$parametros): array {
        return AgrupadorDados::agruparPor($linhas, 'idPedido', $criarObjeto, ...$parametros);
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorItemPizza(array $linhas, callable $criarObjeto, mixed ...$parametros): array {
        return AgrupadorDados::agruparPor($linhas, 'idItemPedidoPizza', $criarObjeto, ...$parametros);
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorSaborEscolhido(array $linhas, callable $criarObjeto, mixed ...$parametros): array {
        return AgrupadorDados::agruparPor($linhas, 'idSaborEscolhido', $criarObjeto, ...$parametros);
    }
}
