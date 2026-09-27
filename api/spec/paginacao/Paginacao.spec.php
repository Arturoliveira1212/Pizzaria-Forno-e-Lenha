<?php

describe('Paginacao', function () {

    describe('validar()', function () {

        it('lança exceção quando a página é menor que o mínimo permitido', function () {
            expect(function () {
                $pagina = Paginacao::PAGINA_MINIMA - 1;
                new Paginacao(pagina: $pagina, limite: Paginacao::LIMITE_PADRAO);
            })->toThrow(new DominioException('A página deve ser maior ou igual a ' . Paginacao::PAGINA_MINIMA . '.'));
        });

        it('lança exceção quando o limite é menor que o mínimo permitido', function () {
            expect(function () {
                $limite = Paginacao::LIMITE_MINIMO_PADRAO - 1;
                new Paginacao(pagina: Paginacao::PAGINA_MINIMA, limite: $limite);
            })->toThrow(new DominioException('O limite deve ser maior ou igual a ' . Paginacao::LIMITE_MINIMO_PADRAO . '.'));
        });

        it('lança exceção quando o limite ultrapassa o máximo permitido', function () {
            expect(function () {
                $limite = Paginacao::LIMITE_MAXIMO_PADRAO + 1;
                new Paginacao(pagina: Paginacao::PAGINA_MINIMA, limite: $limite);
            })->toThrow(new DominioException('O limite deve ser menor ou igual a ' . Paginacao::LIMITE_MAXIMO_PADRAO . '.'));
        });

        it('lança exceção quando o limite é menor que o mínimo permitido (personalizado)', function () {
            expect(function () {
                new Paginacao(pagina: Paginacao::PAGINA_MINIMA, limite: 4, limiteMinimo: 5, limiteMaximo: 50);
            })->toThrow(new DominioException('O limite deve ser maior ou igual a 5.'));
        });

        it('lança exceção quando o limite ultrapassa o máximo permitido (personalizado)', function () {
            expect(function () {
                new Paginacao(pagina: Paginacao::PAGINA_MINIMA, limite: 51, limiteMinimo: 5, limiteMaximo: 50);
            })->toThrow(new DominioException('O limite deve ser menor ou igual a 50.'));
        });

        it('cria a paginação quando todos os campos são válidos (personalizado)', function () {
            $limite = 5;
            $limiteMinimo = 5;
            $limiteMaximo = 50;
            $paginacao = new Paginacao(pagina: Paginacao::PAGINA_MINIMA, limite: $limite, limiteMinimo: $limiteMinimo, limiteMaximo: $limiteMaximo);

            expect($paginacao->pagina)->toBe(Paginacao::PAGINA_MINIMA);
            expect($paginacao->limite)->toBe($limite);
            expect($paginacao->limiteMinimo)->toBe($limiteMinimo);
            expect($paginacao->limiteMaximo)->toBe($limiteMaximo);
        });

        it('cria a paginação quando todos os campos são válidos', function () {
            $paginacao = new Paginacao(pagina: Paginacao::PAGINA_MINIMA, limite: Paginacao::LIMITE_PADRAO);

            expect($paginacao->pagina)->toBe(Paginacao::PAGINA_MINIMA);
            expect($paginacao->limite)->toBe(Paginacao::LIMITE_PADRAO);
            expect($paginacao->limiteMinimo)->toBe(Paginacao::LIMITE_MINIMO_PADRAO);
            expect($paginacao->limiteMaximo)->toBe(Paginacao::LIMITE_MAXIMO_PADRAO);
        });

    });

});
