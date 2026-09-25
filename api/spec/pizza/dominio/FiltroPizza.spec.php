<?php

describe('FiltroPizza', function () {

    $this->mensagemErro = 'Erro ao criar filtro para pizzas.';
    $this->paginacaoValida = new Paginacao();

    it('cria o filtro quando o identificador é vazio, representando "sem filtro" (todas as pizzas)', function () {
        $filtro = new FiltroPizza(identificador: '', paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe('');
    });

    it('cria o filtro quando o identificador tem quantidade de caracteres dentro do permitido', function () {
        $filtro = new FiltroPizza(identificador: 'Artur Alves', paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe('Artur Alves');
    });

    it('lança exceção quando o identificador tem mais caracteres que o máximo permitido', function () {
        expect(function () {
            new FiltroPizza(identificador: str_repeat('a', 101), paginacao: $this->paginacaoValida);
        })->toThrow(new DominioException($this->mensagemErro, ['O identificador deve ter entre 3 e 100 caracteres.']));
    });

    it('cria o filtro quando todos os campos são válidos', function () {
        $filtro = new FiltroPizza(identificador: 'Artur Alves', paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe('Artur Alves');
        expect($filtro->paginacao)->toBe($this->paginacaoValida);
    });

});
