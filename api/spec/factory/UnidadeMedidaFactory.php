<?php

class UnidadeMedidaFactory {
    public static function unidade(): UnidadeMedida {
        return new UnidadeMedida(1, 'unidade', 'un');
    }

    public static function gramas(): UnidadeMedida {
        return new UnidadeMedida(2, 'gramas', 'g');
    }

    public static function litros(): UnidadeMedida {
        return new UnidadeMedida(3, 'litros', 'L');
    }

    public static function folhas(): UnidadeMedida {
        return new UnidadeMedida(4, 'folhas', 'un');
    }
}
