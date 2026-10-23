<?php

class QuantidadePorTamanhoFactory {
    public static function pequena(int $quantidade, UnidadeMedida $unidadeMedida): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: 1,
            tamanhoPizza: TamanhoPizza::PEQUENA,
            quantidade: new Quantidade($quantidade, $unidadeMedida),
        );
    }

    public static function media(int $quantidade, UnidadeMedida $unidadeMedida): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: 2,
            tamanhoPizza: TamanhoPizza::MEDIA,
            quantidade: new Quantidade($quantidade, $unidadeMedida),
        );
    }

    public static function grande(int $quantidade, UnidadeMedida $unidadeMedida): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: 3,
            tamanhoPizza: TamanhoPizza::GRANDE,
            quantidade: new Quantidade($quantidade, $unidadeMedida),
        );
    }
}
