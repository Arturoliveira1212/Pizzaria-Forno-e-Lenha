<?php

interface RepositorioIngrediente {
    public function obterIngredientePorId(int $idIngrediente): ?Ingrediente;
}
