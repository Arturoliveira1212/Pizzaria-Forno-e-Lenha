<?php

class PrecoPorTamanhoFactory {
    public static function pequena(int $centavos): PrecoPorTamanho {
        return new PrecoPorTamanho(
            id: 1,
            tamanhoPizza: TamanhoPizza::PEQUENA,
            preco: new Dinheiro($centavos),
        );
    }

    public static function media(int $centavos): PrecoPorTamanho {
        return new PrecoPorTamanho(
            id: 2,
            tamanhoPizza: TamanhoPizza::MEDIA,
            preco: new Dinheiro($centavos),
        );
    }

    public static function grande(int $centavos): PrecoPorTamanho {
        return new PrecoPorTamanho(
            id: 3,
            tamanhoPizza: TamanhoPizza::GRANDE,
            preco: new Dinheiro($centavos),
        );
    }
}
