<?php

class RepositorioIngredienteEmBDR implements RepositorioIngrediente {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterIngredientePorId(int $idIngrediente): ?Ingrediente {
        return null;
    }
}
