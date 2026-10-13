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
                $idPizza = (int) $linha['id'];
                $pizzas[] = new Pizza(
                    id: $idPizza,
                    nome: $linha['nome'],
                    descricao: $linha['descricao'],
                    ingredientesPadrao: $ingredientesPadrao[$idPizza] ?? [],
                    ingredientesSubstitutos: $ingredientesSubstitutos[$idPizza] ?? [],
                    adicionais: $adicionais[$idPizza] ?? [],
                    precosPorTamanho: $precos[$idPizza] ?? [],
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
            $precos = $this->buscarPrecosPorTamanho([$idPizza]);
            $ingredientesPadrao = $this->buscarIngredientesPadrao([$idPizza]);
            $ingredientesSubstitutos = $this->buscarIngredientesSubstitutos([$idPizza]);
            $adicionais = $this->buscarIngredientesAdicionais([$idPizza]);

            return new Pizza(
                id: (int) $linha['id'],
                nome: $linha['nome'],
                descricao: $linha['descricao'],
                ingredientesPadrao: $ingredientesPadrao[$idPizza] ?? [],
                ingredientesSubstitutos: $ingredientesSubstitutos[$idPizza] ?? [],
                adicionais: $adicionais[$idPizza] ?? [],
                precosPorTamanho: $precos[$idPizza] ?? [],
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter pizza por ID.');
        }
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

        return $this->agruparPorPizza($resultado, function (array $linha): PrecoPorTamanho {
            return new PrecoPorTamanho(
                id: (int) $linha['id'],
                tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
                preco: new Dinheiro((int) $linha['preco']),
            );
        });
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, IngredientePadrao[]>
     */
    private function buscarIngredientesPadrao(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT
            ingrediente_padrao.id AS idIngredientePadrao,
            ingrediente_padrao.idPizza,
            ingrediente_padrao.removivel,
            ingrediente_padrao.substituivel,
            ingrediente.id AS idIngrediente,
            ingrediente.nome AS nomeIngrediente
                FROM ingrediente_padrao
                    INNER JOIN ingrediente ON ingrediente.id = ingrediente_padrao.idIngrediente
                    WHERE ingrediente_padrao.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);

        return $this->agruparPorPizza($resultado, function (array $linha): IngredientePadrao {
            return new IngredientePadrao(
                id: (int) $linha['idIngredientePadrao'],
                ingrediente: new Ingrediente(
                    id: (int) $linha['idIngrediente'],
                    nome: $linha['nomeIngrediente'],
                ),
                removivel: (bool) $linha['removivel'],
                substituivel: (bool) $linha['substituivel'],
            );
        });
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, Ingrediente[]>
     */
    private function buscarIngredientesSubstitutos(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT DISTINCT
            ingrediente_substituto.idPizza,
            ingrediente.id AS idIngrediente,
            ingrediente.nome AS nomeIngrediente
                FROM ingrediente_substituto
                    INNER JOIN ingrediente
                        ON ingrediente.id = ingrediente_substituto.idIngrediente
                    WHERE ingrediente_substituto.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);

        return $this->agruparPorPizza($resultado, function (array $linha): Ingrediente {
            return new Ingrediente(
                id: (int) $linha['idIngrediente'],
                nome: $linha['nomeIngrediente'],
            );
        });
    }

    /**
     * @param int[] $idsPizzas
     * @return array<int, IngredienteAdicional[]>
     */
    private function buscarIngredientesAdicionais(array $idsPizzas): array {
        $idsPizzasString = implode(',', $idsPizzas);
        $sql = "SELECT
            ingrediente_adicional.id AS idIngredienteAdicional,
            adicional_pizza.idPizza,
            ingrediente_adicional.preco,
            ingrediente.id AS idIngrediente,
            ingrediente.nome AS nomeIngrediente
                FROM ingrediente_adicional
                    INNER JOIN adicional_pizza
                        ON adicional_pizza.idIngredienteAdicional = ingrediente_adicional.id
                    INNER JOIN ingrediente ON ingrediente.id = ingrediente_adicional.idIngrediente
                    WHERE adicional_pizza.idPizza IN ({$idsPizzasString})";

        $resultado = $this->bancoDados->consultar($sql);

        return $this->agruparPorPizza($resultado, function (array $linha): IngredienteAdicional {
            return new IngredienteAdicional(
                id: (int) $linha['idIngredienteAdicional'],
                ingrediente: new Ingrediente(
                    id: (int) $linha['idIngrediente'],
                    nome: $linha['nomeIngrediente'],
                ),
                preco: new Dinheiro((int) $linha['preco']),
            );
        });
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    private function agruparPorPizza(array $linhas, callable $criarObjeto): array {
        $agrupado = [];

        foreach ($linhas as $linha) {
            $idPizza = (int) $linha['idPizza'];
            $agrupado[$idPizza][] = $criarObjeto($linha);
        }

        return $agrupado;
    }
}
