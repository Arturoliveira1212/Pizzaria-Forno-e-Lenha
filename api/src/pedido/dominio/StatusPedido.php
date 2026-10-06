<?php

enum StatusPedido: string {
    case RECEBIDO = 'RECEBIDO';
    case EM_PREPARACAO = 'EM_PREPARACAO';
    case SAIU_PARA_ENTREGA = 'SAIU_PARA_ENTREGA';
    case PRONTO_PARA_RETIRADA = 'PRONTO_PARA_RETIRADA';
    case FINALIZADO = 'FINALIZADO';
    case CANCELADO = 'CANCELADO';

    public static function inicial(): self {
        return self::RECEBIDO;
    }

    /**
     * @return self[]
     */
    public function proximos(TipoAtendimento $tipoAtendimento): array {
        return match ($this) {
            self::RECEBIDO => [self::EM_PREPARACAO, self::CANCELADO],
            self::EM_PREPARACAO => match ($tipoAtendimento) {
                TipoAtendimento::ENTREGA => [self::SAIU_PARA_ENTREGA, self::CANCELADO],
                TipoAtendimento::RETIRADA => [self::PRONTO_PARA_RETIRADA, self::CANCELADO],
                TipoAtendimento::BALCAO => [self::FINALIZADO, self::CANCELADO],
            },
            self::SAIU_PARA_ENTREGA, self::PRONTO_PARA_RETIRADA => [self::FINALIZADO, self::CANCELADO],
            self::FINALIZADO, self::CANCELADO => [],
        };
    }
}
