<?php

enum TamanhoPizza: string {
    case PEQUENA = 'PEQUENA';
    case MEDIA = 'MEDIA';
    case GRANDE = 'GRANDE';

    public function obterRotulo(): string {
        return match ($this) {
            self::PEQUENA => 'P',
            self::MEDIA => 'M',
            self::GRANDE => 'G',
        };
    }
}
