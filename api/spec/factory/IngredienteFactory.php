<?php

class IngredienteFactory {
    public static function molho(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::litros());

        return new Ingrediente(1, 'Molho', $estoque);
    }

    public static function mussarela(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(2, 'Mussarela', $estoque);
    }

    public static function presunto(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(3, 'Presunto', $estoque);
    }

    public static function azeitona(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(4, 'Azeitona', $estoque);
    }

    public static function cebola(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(5, 'Cebola', $estoque);
    }

    public static function milho(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(6, 'Milho', $estoque);
    }

    public static function bacon(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(7, 'Bacon', $estoque);
    }

    public static function catupiry(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(8, 'Catupiry', $estoque);
    }

    public static function manjericao(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::folhas());

        return new Ingrediente(9, 'Manjericão', $estoque);
    }

    public static function calabresa(int $estoque = 1): Ingrediente {
        $estoque = new QuantidadeMedida($estoque, UnidadeMedidaFactory::gramas());

        return new Ingrediente(10, 'Calabresa', $estoque);
    }
}
