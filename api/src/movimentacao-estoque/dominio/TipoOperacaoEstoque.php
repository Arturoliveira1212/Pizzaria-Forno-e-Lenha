<?php

enum TipoOperacaoEstoque: string {
    case ENTRADA = 'ENTRADA';
    case SAIDA = 'SAIDA';

    public function sinal(): string {
        return match ($this) {
            self::ENTRADA => '+',
            self::SAIDA => '-',
        };
    }
}
