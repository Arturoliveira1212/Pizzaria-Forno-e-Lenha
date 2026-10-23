<?php

class IngredienteSubstitutoFactory {
    public static function milho(): IngredienteSubstituto {
        return new IngredienteSubstituto(
            id: 2,
            ingrediente: IngredienteFactory::milho(),
            quantidadesPorTamanho: [
                new QuantidadePorTamanho(
                    id: 1,
                    tamanhoPizza: TamanhoPizza::PEQUENA,
                    quantidade: new Quantidade(150, UnidadeMedidaFactory::gramas()),
                ),
                new QuantidadePorTamanho(
                    id: 2,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    quantidade: new Quantidade(220, UnidadeMedidaFactory::gramas()),
                ),
                new QuantidadePorTamanho(
                    id: 3,
                    tamanhoPizza: TamanhoPizza::GRANDE,
                    quantidade: new Quantidade(300, UnidadeMedidaFactory::gramas()),
                ),
            ],
        );
    }

    public static function cebola(): IngredienteSubstituto {
        return new IngredienteSubstituto(
            id: 1,
            ingrediente: IngredienteFactory::cebola(),
            quantidadesPorTamanho: [
                new QuantidadePorTamanho(
                    id: 1,
                    tamanhoPizza: TamanhoPizza::PEQUENA,
                    quantidade: new Quantidade(150, UnidadeMedidaFactory::gramas()),
                ),
                new QuantidadePorTamanho(
                    id: 2,
                    tamanhoPizza: TamanhoPizza::MEDIA,
                    quantidade: new Quantidade(200, UnidadeMedidaFactory::gramas()),
                ),
                new QuantidadePorTamanho(
                    id: 3,
                    tamanhoPizza: TamanhoPizza::GRANDE,
                    quantidade: new Quantidade(300, UnidadeMedidaFactory::gramas()),
                ),
            ],
        );
    }
}
