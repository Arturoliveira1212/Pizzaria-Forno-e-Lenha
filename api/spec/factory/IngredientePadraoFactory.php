<?php

class IngredientePadraoFactory {
    public static function molho(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 1,
            ingrediente: IngredienteFactory::molho(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(1, UnidadeMedidaFactory::litros()),
                QuantidadePorTamanhoFactory::media(2, UnidadeMedidaFactory::litros()),
                QuantidadePorTamanhoFactory::grande(4, UnidadeMedidaFactory::litros()),
            ],
        );
    }

    public static function mussarela(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 2,
            ingrediente: IngredienteFactory::mussarela(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(100, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::media(200, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::grande(300, UnidadeMedidaFactory::gramas()),
            ],
        );
    }

    public static function presunto(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 6,
            ingrediente: IngredienteFactory::presunto(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(100, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::media(200, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::grande(300, UnidadeMedidaFactory::gramas()),
            ],
        );
    }

    public static function catupiry(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 4,
            ingrediente: IngredienteFactory::catupiry(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(100, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::media(200, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::grande(300, UnidadeMedidaFactory::gramas()),
            ],
        );
    }

    public static function manjericao(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 3,
            ingrediente: IngredienteFactory::manjericao(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(1, UnidadeMedidaFactory::folhas()),
                QuantidadePorTamanhoFactory::media(2, UnidadeMedidaFactory::folhas()),
                QuantidadePorTamanhoFactory::grande(4, UnidadeMedidaFactory::folhas()),
            ],
        );
    }

    public static function calabresa(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 5,
            ingrediente: IngredienteFactory::calabresa(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(100, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::media(200, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::grande(300, UnidadeMedidaFactory::gramas()),
            ],
        );
    }

    public static function azeitona(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 7,
            ingrediente: IngredienteFactory::azeitona(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(50, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::media(100, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::grande(150, UnidadeMedidaFactory::gramas()),
            ],
        );
    }

    public static function bacon(bool $removivel = false, bool $substituivel = false): IngredientePadrao {
        return new IngredientePadrao(
            id: 8,
            ingrediente: IngredienteFactory::bacon(),
            removivel: $removivel,
            substituivel: $substituivel,
            quantidadesPorTamanho: [
                QuantidadePorTamanhoFactory::pequena(100, UnidadeMedidaFactory::gramas()),
                QuantidadePorTamanhoFactory::media(200, UnidadeMedidaFactory::gramas()),
            ],
        );
    }
}
