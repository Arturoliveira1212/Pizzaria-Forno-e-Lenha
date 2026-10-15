<?php

class GestorPizza {
    public function __construct(
        private readonly RepositorioPizza $repositorio,
    ) {}

    /**
     * @return PizzaParaCardapio[]
     */
    public function obterPizzasParaCardapio(RestricoesParaBuscaDePizzas $restricoes): array {
        $filtro = $this->criarFiltro($restricoes);

        return $this->repositorio->obterPizzasParaCardapio($filtro);
    }

    /**
     * @return Pizza[]
     */
    public function obterPizzas(RestricoesParaBuscaDePizzas $restricoes): array {
        $filtro = $this->criarFiltro($restricoes);

        return $this->repositorio->obterPizzas($filtro);
    }

    private function criarFiltro(RestricoesParaBuscaDePizzas $restricoes): FiltroPizza {
        return new FiltroPizza(
            identificador: trim($restricoes->identificador ?? ''),
            paginacao: new Paginacao(
                pagina: $restricoes->pagina ?? Paginacao::PAGINA_PADRAO,
                limite: $restricoes->limite ?? Paginacao::LIMITE_PADRAO,
            ),
        );
    }

    public function obterPizzaPorId(int $id): Pizza {
        $pizza = $this->repositorio->obterPizzaPorId($id);
        if (!$pizza instanceof Pizza) {
            throw new NaoEncontradoException('Pizza não encontrada.');
        }

        return $pizza;
    }
}
