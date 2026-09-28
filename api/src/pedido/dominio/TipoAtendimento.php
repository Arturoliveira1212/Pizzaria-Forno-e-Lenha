<?php

enum TipoAtendimento: string {
    case BALCAO = 'BALCAO';
    case ENTREGA = 'ENTREGA';
    case RETIRADA = 'RETIRADA';

    public function exigeCliente(): bool {
        return $this === self::ENTREGA;
    }
}
