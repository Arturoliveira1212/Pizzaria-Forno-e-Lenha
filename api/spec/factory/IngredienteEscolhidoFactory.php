<?php

class IngredienteEscolhidoFactory {
    public static function padrao(int $id, Ingrediente $ingrediente): IngredienteEscolhido {
        return new IngredienteEscolhido(
            id: $id,
            ingrediente: $ingrediente,
            papel: PapelIngrediente::PADRAO,
            custoAdicional: Dinheiro::zero(),
        );
    }

    public static function substituto(int $id, Ingrediente $ingrediente): IngredienteEscolhido {
        return new IngredienteEscolhido(
            id: $id,
            ingrediente: $ingrediente,
            papel: PapelIngrediente::SUBSTITUTO,
            custoAdicional: Dinheiro::zero(),
        );
    }

    public static function adicional(int $id, Ingrediente $ingrediente, int $custoAdicional): IngredienteEscolhido {
        return new IngredienteEscolhido(
            id: $id,
            ingrediente: $ingrediente,
            papel: PapelIngrediente::ADICIONAL,
            custoAdicional: new Dinheiro($custoAdicional),
        );
    }
}
