<?php

class PizzaFactory {
    public static function calabresa(): Pizza {
        return new Pizza(
            id: 3,
            nome: 'Calabresa',
            descricao: 'Pizza de Calabresa',
            ingredientesPadrao: [
                IngredientePadraoFactory::molho(false, false),
                IngredientePadraoFactory::mussarela(false, false),
                IngredientePadraoFactory::calabresa(true, true),
            ],
            ingredientesSubstitutos: [
                IngredienteSubstitutoFactory::cebola(),
                IngredienteSubstitutoFactory::milho(),
            ],
            adicionais: [
                IngredienteAdicionalFactory::bacon(),
            ],
            precosPorTamanho: [
                PrecoPorTamanhoFactory::pequena(2290),
                PrecoPorTamanhoFactory::media(3290),
                PrecoPorTamanhoFactory::grande(4399),
            ],
        );
    }

    public static function margherita(): Pizza {
        return new Pizza(
            id: 2,
            nome: 'Margherita',
            descricao: 'Pizza de Margherita',
            ingredientesPadrao: [
                IngredientePadraoFactory::molho(false, false),
                IngredientePadraoFactory::mussarela(false, false),
                IngredientePadraoFactory::catupiry(true, true),
                IngredientePadraoFactory::manjericao(true, true),
            ],
            ingredientesSubstitutos: [
                IngredienteSubstitutoFactory::cebola(),
                IngredienteSubstitutoFactory::milho(),
            ],
            adicionais: [
                IngredienteAdicionalFactory::bacon(),
                IngredienteAdicionalFactory::calabresa(),
                IngredienteAdicionalFactory::catupiry(),
            ],
            precosPorTamanho: [
                PrecoPorTamanhoFactory::pequena(2190),
                PrecoPorTamanhoFactory::media(3090),
                PrecoPorTamanhoFactory::grande(4599),
            ],
        );
    }

    public static function portuguesa(): Pizza {
        return new Pizza(
            id: 4,
            nome: 'Portuguesa',
            descricao: 'Pizza de Portuguesa',
            ingredientesPadrao: [
                IngredientePadraoFactory::molho(false, false),
                IngredientePadraoFactory::mussarela(false, false),
                IngredientePadraoFactory::presunto(true, true),
                IngredientePadraoFactory::azeitona(true, true),
            ],
            ingredientesSubstitutos: [
                IngredienteSubstitutoFactory::cebola(),
                IngredienteSubstitutoFactory::milho(),
            ],
            adicionais: [
                IngredienteAdicionalFactory::bacon(),
                IngredienteAdicionalFactory::catupiry(),
            ],
            precosPorTamanho: [
                PrecoPorTamanhoFactory::pequena(2990),
                PrecoPorTamanhoFactory::media(3990),
            ],
        );
    }

    public static function quatroQueijos(): Pizza {
        return new Pizza(
            id: 5,
            nome: 'Quatro Queijos',
            descricao: 'Pizza de Quatro Queijos',
            ingredientesPadrao: [
                IngredientePadraoFactory::molho(false, false),
                IngredientePadraoFactory::mussarela(false, false),
                IngredientePadraoFactory::catupiry(true, true),
                IngredientePadraoFactory::presunto(true, true),
            ],
            ingredientesSubstitutos: [
                IngredienteSubstitutoFactory::cebola(),
                IngredienteSubstitutoFactory::milho(),
            ],
            adicionais: [
                IngredienteAdicionalFactory::bacon(),
                IngredienteAdicionalFactory::calabresa(),
            ],
            precosPorTamanho: [
                PrecoPorTamanhoFactory::pequena(2599),
                PrecoPorTamanhoFactory::media(3599),
                PrecoPorTamanhoFactory::grande(4590),
            ],
        );
    }
}
