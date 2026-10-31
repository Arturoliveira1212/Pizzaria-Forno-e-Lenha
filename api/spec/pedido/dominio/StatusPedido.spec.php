<?php

describe('StatusPedido', function () {

    describe('proximos()', function () {

        it('retorna cancelado entre os próximos status quando o pedido está em recebido', function () {
            $proximos = StatusPedido::RECEBIDO->proximos(TipoAtendimento::ENTREGA);

            expect($proximos)->toContain(StatusPedido::CANCELADO);
        });

        it('retorna apenas finalizado quando o pedido saiu para entrega', function () {
            $proximos = StatusPedido::SAIU_PARA_ENTREGA->proximos(TipoAtendimento::ENTREGA);

            expect($proximos)->toBe([StatusPedido::FINALIZADO]);
        });

        it('retorna apenas finalizado quando o pedido está pronto para retirada', function () {
            $proximos = StatusPedido::PRONTO_PARA_RETIRADA->proximos(TipoAtendimento::RETIRADA);

            expect($proximos)->toBe([StatusPedido::FINALIZADO]);
        });

    });

});
