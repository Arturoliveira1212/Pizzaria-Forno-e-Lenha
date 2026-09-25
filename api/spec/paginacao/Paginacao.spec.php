<?php

describe('Paginacao', function () {

    $this->mensagemErro = 'Erro ao criar paginação.';

    it('cria a paginação quando a página tem valor dentro do permitido', function () {
        $filtro = new Paginacao(pagina: 1, limite: Paginacao::LIMITE_PADRAO);

        expect($filtro->pagina)->toBe(1);
    });

    it('lança exceção quando a página é menor que o mínimo permitido', function () {
        expect(function () {
            new Paginacao(pagina: 0, limite: Paginacao::LIMITE_PADRAO);
        })->toThrow(new DominioException($this->mensagemErro, ['A página deve ser maior ou igual a 1.']));
    });

    it('cria a paginação quando o limite tem valor dentro do permitido', function () {
        $filtro = new Paginacao(pagina: 1, limite: 10);

        expect($filtro->limite)->toBe(10);
    });

    it('lança exceção quando o limite é menor que o mínimo permitido', function () {
        expect(function () {
            new Paginacao(pagina: 1, limite: 0);
        })->toThrow(new DominioException($this->mensagemErro, ['O limite deve ser maior ou igual a 1.']));
    });

    it('lança exceção quando o limite ultrapassa o máximo permitido', function () {
        expect(function () {
            new Paginacao(pagina: 1, limite: 101);
        })->toThrow(new DominioException($this->mensagemErro, ['O limite deve ser menor ou igual a 100.']));
    });

    it('cria a paginação quando o limite tem valor dentro do permitido (personalizado)', function () {
        $filtro = new Paginacao(pagina: 1, limite: 5, limiteMinimo: 5, limiteMaximo: 50);

        expect($filtro->limite)->toBe(5);
    });

    it('lança exceção quando o limite é menor que o mínimo permitido (personalizado)', function () {
        expect(function () {
            new Paginacao(pagina: 1, limite: 4, limiteMinimo: 5, limiteMaximo: 50);
        })->toThrow(new DominioException($this->mensagemErro, ['O limite deve ser maior ou igual a 5.']));
    });

    it('lança exceção quando o limite ultrapassa o máximo permitido (personalizado)', function () {
        expect(function () {
            new Paginacao(pagina: 1, limite: 51, limiteMinimo: 5, limiteMaximo: 50);
        })->toThrow(new DominioException($this->mensagemErro, ['O limite deve ser menor ou igual a 50.']));
    });

    it('cria a paginação quando todos os campos são válidos', function () {
        $filtro = new Paginacao(
            pagina: 2,
            limite: 25,
        );

        expect($filtro->pagina)->toBe(2);
        expect($filtro->limite)->toBe(25);
    });

});
