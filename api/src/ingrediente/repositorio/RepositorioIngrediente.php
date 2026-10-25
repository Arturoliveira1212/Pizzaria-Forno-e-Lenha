<?php

interface RepositorioIngrediente {
    public function obterIngredientePorId(int $idIngrediente): ?Ingrediente;

    public function atualizarEstoqueIngrediente(int $idIngrediente, QuantidadeMedida $quantidade, TipoOperacaoEstoque $tipoOperacaoEstoque): void;
}
