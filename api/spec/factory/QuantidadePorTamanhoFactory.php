<?php

class QuantidadePorTamanhoFactory {
    public static function pequena(int $quantidade, UnidadeMedida $unidadeMedida): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: 1,
            tamanhoPizza: TamanhoPizza::PEQUENA,
            quantidadeMedida: new QuantidadeMedida($quantidade, $unidadeMedida),
        );
    }

    public static function media(int $quantidade, UnidadeMedida $unidadeMedida): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: 2,
            tamanhoPizza: TamanhoPizza::MEDIA,
            quantidadeMedida: new QuantidadeMedida($quantidade, $unidadeMedida),
        );
    }

    public static function grande(int $quantidade, UnidadeMedida $unidadeMedida): QuantidadePorTamanho {
        return new QuantidadePorTamanho(
            id: 3,
            tamanhoPizza: TamanhoPizza::GRANDE,
            quantidadeMedida: new QuantidadeMedida($quantidade, $unidadeMedida),
        );
    }
}
