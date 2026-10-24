<?php

class RepositorioPizzaEmBDR implements RepositorioPizza {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPizzasParaCardapio(FiltroPizza $filtro): array {
        try {
            $parametros = [];
            $resultado = $this->bancoDados->consultar($this->gerarQuery($filtro, $parametros), $parametros);
            if (empty($resultado)) {
                return [];
            }

            $idsPizzas = array_map('intval', array_column($resultado, 'id'));
            $precos = $this->buscarPrecosPorTamanho($idsPizzas);
            $ingredientesPadrao = $this->buscarIngredientesPadrao($idsPizzas);

            $pizzas = [];
            foreach ($resultado as $linha) {
                $idPizza = (int) $linha['id'];
                $ingredientes = $ingredientesPadrao[$idPizza]
                    ? array_map(function (IngredientePadrao $ingredientePadrao) {
                        return $ingredientePadrao->ingrediente;
                    }, $ingredientesPadrao[$idPizza])
                    : [];

                $pizzas[] = new PizzaParaCardapio(
                    id: $idPizza,
                    nome: $linha['nome'],
                    descricao: $linha['descricao'],
                    precosPorTamanho: $precos[$idPizza] ?? [],
                    ingredientes: $ingredientes,
                );
            }

            return $pizzas;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pizzas para o cardápio.');
        }
    }

    public function obterPizzas(FiltroPizza $filtro): array {
        try {
            $parametros = [];
            $resultado = $this->bancoDados->consultar($this->gerarQuery($filtro, $parametros), $parametros);
            if (empty($resultado)) {
                return [];
            }

            $idsPizzas = array_map('intval', array_column($resultado, 'id'));
            $precos = $this->buscarPrecosPorTamanho($idsPizzas);
            $ingredientesPadrao = $this->buscarIngredientesPadrao($idsPizzas);
            $ingredientesSubstitutos = $this->buscarIngredientesSubstitutos($idsPizzas);
            $adicionais = $this->buscarIngredientesAdicionais($idsPizzas);

            $pizzas = [];
            foreach ($resultado as $linha) {
                $pizzas[] = $this->criarPizza(
                    linha: $linha,
                    ingredientesPadrao: $ingredientesPadrao,
                    ingredientesSubstitutos: $ingredientesSubstitutos,
                    adicionais: $adicionais,
                    precos: $precos,
                );
            }

            return $pizzas;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pizzas.');
        }
    }

    public function obterPizzaPorId(int $idPizza): ?Pizza {
        try {
            $resultado = $this->bancoDados->consultar(
                'SELECT pizza.id, pizza.nome, pizza.descricao
                    FROM pizza
                    WHERE pizza.id = :id',
                ['id' => $idPizza],
            );
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];
            $idPizza = (int) $linha['id'];
            $precos = $this->buscarPrecosPorTamanho([$idPizza]);
            $ingredientesPadrao = $this->buscarIngredientesPadrao([$idPizza]);
            $ingredientesSubstitutos = $this->buscarIngredientesSubstitutos([$idPizza]);
            $adicionais = $this->buscarIngredientesAdicionais([$idPizza]);

            return $this->criarPizza(
                linha: $linha,
                ingredientesPadrao: $ingredientesPadrao,
                ingredientesSubstitutos: $ingredientesSubstitutos,
                adicionais: $adicionais,
                precos: $precos,
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pizza por ID.' . $e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $linha
     * @param array<int, IngredientePadrao[]> $ingredientesPadrao
     * @param array<int, IngredienteSubstituto[]> $ingredientesSubstitutos
     * @param array<int, IngredienteAdicional[]> $adicionais
     * @param array<int, PrecoPorTamanho[]> $precos
     */
    private function criarPizza(array $linha, array $ingredientesPadrao, array $ingredientesSubstitutos, array $adicionais, array $precos): Pizza {
        $idPizza = (int) $linha['id'];

        return new Pizza(
            id: $idPizza,
            nome: $linha['nome'],
            descricao: $linha['descricao'],
            ingredientesPadrao: $ingredientesPadrao[$idPizza] ?? [],
            ingredientesSubstitutos: $ingredientesSubstitutos[$idPizza] ?? [],
            adicionais: $adicionais[$idPizza] ?? [],
            precosPorTamanho: $precos[$idPizza] ?? [],
        );
    }

    /**
     * @param array<string, mixed> $parametros
     */
    private function gerarQuery(FiltroPizza $filtro, array &$parametros): string {
        $query = 'SELECT
            pizza.id,
            pizza.nome,
            pizza.descricao
                FROM pizza
                    WHERE 1=1';

        if ($filtro->identificador !== '') {
            $query .= ' AND (pizza.nome LIKE :nome OR pizza.id = :id)';
            $parametros['nome'] = "%{$filtro->identificador}%";
            $parametros['id'] = $filtro->identificador;
        }

        if ($filtro->paginacao !== null) {
            $limite = $filtro->paginacao->limite;
            $offset = ($filtro->paginacao->pagina - 1) * $limite;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return $query;
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, PrecoPorTamanho[]>
     */
    private function buscarPrecosPorTamanho(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT
            preco_por_tamanho.id,
            preco_por_tamanho.idPizza,
            preco_por_tamanho.tamanho,
            preco_por_tamanho.preco
                FROM preco_por_tamanho
                    WHERE preco_por_tamanho.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);

        return $this->agruparPorPizza($resultado, $this->criarPrecoPorTamanho(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarPrecoPorTamanho(array $linha): PrecoPorTamanho {
        return new PrecoPorTamanho(
            id: (int) $linha['id'],
            tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
            preco: new Dinheiro((int) $linha['preco']),
        );
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, IngredientePadrao[]>
     */
    private function buscarIngredientesPadrao(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT
            ingrediente_padrao.id,
            ingrediente_padrao.idPizza,
            ingrediente_padrao.removivel,
            ingrediente_padrao.substituivel,
            ingrediente.id AS idIngrediente,
            ingrediente.nome AS nomeIngrediente,
            ingrediente.estoque,
            ingrediente.idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM ingrediente_padrao
                    INNER JOIN ingrediente ON ingrediente.id = ingrediente_padrao.idIngrediente
                    INNER JOIN unidade_medida ON unidade_medida.id = ingrediente.idUnidadeMedida
                        WHERE ingrediente_padrao.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        $idsIngredientesPadrao = array_map('intval', array_column($resultado, 'id'));
        $quantidades = $this->buscarQuantidadesIngredientePadrao($idsIngredientesPadrao);

        return $this->agruparPorPizza($resultado, $this->criarIngredientePadrao(...), $quantidades);
    }

    /**
     * @param array<string, mixed> $linha
     * @param array<int, QuantidadePorTamanho[]> $quantidades
     */
    private function criarIngredientePadrao(array $linha, array $quantidades): IngredientePadrao {
        $idIngredientePadrao = (int) $linha['id'];

        return new IngredientePadrao(
            id: $idIngredientePadrao,
            ingrediente: new Ingrediente(
                id: (int) $linha['idIngrediente'],
                nome: $linha['nomeIngrediente'],
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedida'],
                        descricao: $linha['descricao'],
                        sigla: $linha['sigla'],
                    ),
                ),
            ),
            removivel: (bool) $linha['removivel'],
            substituivel: (bool) $linha['substituivel'],
            quantidadesPorTamanho: $quantidades[$idIngredientePadrao] ?? [],
        );
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, IngredienteSubstituto[]>
     */
    private function buscarIngredientesSubstitutos(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT
            ingrediente_substituto.id,
            ingrediente_substituto.idPizza,
            ingrediente.id AS idIngrediente,
            ingrediente.nome AS nomeIngrediente,
            ingrediente.estoque,
            ingrediente.idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM ingrediente_substituto
                    INNER JOIN ingrediente ON ingrediente.id = ingrediente_substituto.idIngrediente
                    INNER JOIN unidade_medida ON unidade_medida.id = ingrediente.idUnidadeMedida
                        WHERE ingrediente_substituto.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);
        if (empty($resultado)) {
            return [];
        }

        $idsIngredientesSubstitutos = array_map('intval', array_column($resultado, 'id'));
        $quantidades = $this->buscarQuantidadesIngredienteSubstituto($idsIngredientesSubstitutos);

        return $this->agruparPorPizza($resultado, $this->criarIngredienteSubstituto(...), $quantidades);
    }

    /**
     * @param array<string, mixed> $linha
     * @param array<int, QuantidadePorTamanho[]> $quantidades
     */
    private function criarIngredienteSubstituto(array $linha, array $quantidades): IngredienteSubstituto {
        $idIngredienteSubstituto = (int) $linha['id'];

        return new IngredienteSubstituto(
            id: $idIngredienteSubstituto,
            ingrediente: new Ingrediente(
                id: (int) $linha['idIngrediente'],
                nome: $linha['nomeIngrediente'],
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedida'],
                        descricao: $linha['descricao'],
                        sigla: $linha['sigla'],
                    ),
                ),
            ),
            quantidadesPorTamanho: $quantidades[$idIngredienteSubstituto] ?? [],
        );
    }

    /**
     * @param int[] $ids
     * @return array<int, QuantidadePorTamanho[]>
     */
    private function buscarQuantidadesPorTamanho(string $tabela, string $colunaId, array $ids): array {
        $idsString = implode(',', array_map('intval', $ids));
        $sql = "SELECT
            {$tabela}.id,
            {$tabela}.{$colunaId},
            {$tabela}.tamanho,
            {$tabela}.quantidade,
            {$tabela}.idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM {$tabela}
                        INNER JOIN unidade_medida ON unidade_medida.id = {$tabela}.idUnidadeMedida
                            WHERE {$tabela}.{$colunaId} IN ({$idsString})";

        $resultado = $this->bancoDados->consultar($sql);

        if ($colunaId === 'idIngredientePadrao') {
            return $this->agruparPorIngredientePadrao($resultado, $this->criarQuantidadePorTamanho(...));
        }

        return $this->agruparPorIngredienteSubstituto($resultado, $this->criarQuantidadePorTamanho(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarQuantidadePorTamanho(array $linha): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: (int) $linha['id'],
            tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
            quantidadeMedida: new QuantidadeMedida(
                valor: (int) $linha['quantidade'],
                unidadeMedida: new UnidadeMedida(
                    id: (int) $linha['idUnidadeMedida'],
                    descricao: $linha['descricao'],
                    sigla: $linha['sigla'],
                ),
            ),
        );
    }

    /**
     * @param int[] $ids
     * @return array<int, QuantidadePorTamanho[]>
     */
    private function buscarQuantidadesIngredientePadrao(array $ids): array {
        return $this->buscarQuantidadesPorTamanho('ingrediente_padrao_quantidade', 'idIngredientePadrao', $ids);
    }

    /**
     * @param int[] $ids
     * @return array<int, QuantidadePorTamanho[]>
     */
    private function buscarQuantidadesIngredienteSubstituto(array $ids): array {
        return $this->buscarQuantidadesPorTamanho('ingrediente_substituto_quantidade', 'idIngredienteSubstituto', $ids);
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, IngredienteAdicional[]>
     */
    private function buscarIngredientesAdicionais(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT
            ingrediente_adicional.id,
            adicional_pizza.idPizza,
            ingrediente_adicional.preco,
            ingrediente_adicional.quantidade,
            ingrediente_adicional.idIngrediente,
            ingrediente.nome AS nomeIngrediente,
            ingrediente.estoque,
            unidade_medida.id AS idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM ingrediente_adicional
                    INNER JOIN adicional_pizza ON adicional_pizza.idIngredienteAdicional = ingrediente_adicional.id
                    INNER JOIN ingrediente ON ingrediente.id = ingrediente_adicional.idIngrediente
                    INNER JOIN unidade_medida ON unidade_medida.id = ingrediente.idUnidadeMedida
                        WHERE adicional_pizza.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);

        return $this->agruparPorPizza($resultado, $this->criarIngredienteAdicional(...));
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarIngredienteAdicional(array $linha): IngredienteAdicional {
        return new IngredienteAdicional(
            id: (int) $linha['id'],
            ingrediente: new Ingrediente(
                id: (int) $linha['idIngrediente'],
                nome: $linha['nomeIngrediente'],
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedida'],
                        descricao: $linha['descricao'],
                        sigla: $linha['sigla'],
                    ),
                ),
            ),
            preco: new Dinheiro((int) $linha['preco']),
            quantidadeMedida: new QuantidadeMedida(
                valor: (int) $linha['quantidade'],
                unidadeMedida: new UnidadeMedida(
                    id: (int) $linha['idUnidadeMedida'],
                    descricao: $linha['descricao'],
                    sigla: $linha['sigla'],
                ),
            ),
        );
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPor(array $linhas, string $chave, callable $criarObjeto, mixed ...$parametros): array {
        $agrupado = [];

        foreach ($linhas as $linha) {
            $id = (int) $linha[$chave];
            $agrupado[$id][] = $criarObjeto($linha, ...$parametros);
        }

        return $agrupado;
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorPizza(array $linhas, callable $criarObjeto, mixed ...$parametros): array {
        return $this->agruparPor($linhas, 'idPizza', $criarObjeto, ...$parametros);
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorIngredientePadrao(array $linhas, callable $criarObjeto, mixed ...$parametros): array {
        return $this->agruparPor($linhas, 'idIngredientePadrao', $criarObjeto, ...$parametros);
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorIngredienteSubstituto(array $linhas, callable $criarObjeto, mixed ...$parametros): array {
        return $this->agruparPor($linhas, 'idIngredienteSubstituto', $criarObjeto, ...$parametros);
    }
}
