<?php

class IngredienteAdicionalFactory {
    public static function bacon(): IngredienteAdicional {
        return new IngredienteAdicional(
            id: 1,
            ingrediente: IngredienteFactory::bacon(),
            preco: new Dinheiro(899),
            quantidadeMedida: new QuantidadeMedida(350, UnidadeMedidaFactory::gramas()),
        );
    }

    public static function calabresa(): IngredienteAdicional {
        return new IngredienteAdicional(
            id: 2,
            ingrediente: IngredienteFactory::calabresa(),
            preco: new Dinheiro(799),
            quantidadeMedida: new QuantidadeMedida(250, UnidadeMedidaFactory::gramas()),
        );
    }

    public static function catupiry(): IngredienteAdicional {
        return new IngredienteAdicional(
            id: 3,
            ingrediente: IngredienteFactory::catupiry(),
            preco: new Dinheiro(799),
            quantidadeMedida: new QuantidadeMedida(250, UnidadeMedidaFactory::gramas()),
        );
    }
}
