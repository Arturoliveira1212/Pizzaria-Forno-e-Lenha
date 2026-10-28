<?php

describe('Cancelamento', function () {

    describe('validar()', function () {

        it('lança exceção quando o motivo está vazio', function () {
            expect(function () {
                new Cancelamento(
                    id: 0,
                    motivo: '',
                    data: new DateTime(),
                );
            })->toThrow(new DominioException('O motivo do cancelamento é obrigatório.'));
        });

        it('lança exceção quando o motivo contém apenas espaços', function () {
            expect(function () {
                new Cancelamento(
                    id: 0,
                    motivo: '   ',
                    data: new DateTime(),
                );
            })->toThrow(new DominioException('O motivo do cancelamento é obrigatório.'));
        });

        it('cria o cancelamento quando todos os campos são válidos', function () {
            $cancelamento = new Cancelamento(
                id: 0,
                motivo: 'Cliente desistiu do pedido',
                data: new DateTime(),
            );

            expect($cancelamento->motivo)->toBe('Cliente desistiu do pedido');
        });

    });

});
