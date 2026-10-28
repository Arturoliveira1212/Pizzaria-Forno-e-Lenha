<?php

class EntregadorFactory {
    public static function entregador(bool $disponivel = true): Entregador {
        return new Entregador(
            id: 2,
            nome: 'Marcos Vinícius Rocha',
            telefone: Telefone::criar('21976543210'),
            disponivel: $disponivel,
        );
    }
}
