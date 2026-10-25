<?php

interface RepositorioIngrediente {
    public function obterIngredientePorId(int $idIngrediente): ?Ingrediente;

    public function atualizarEstoqueIngrediente(int $idProduto, QuantidadeMedida $quantidade, TipoOperacaoEstoque $tipoOperacaoEstoque): void;
}
