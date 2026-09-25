<?php

describe('FiltroCliente', function () {

    $this->mensagemErro = 'Erro ao filtrar clientes.';

    it('cria o filtro quando o identificador é vazio, representando "sem filtro" (todos os clientes)', function () {
        $filtro = new FiltroCliente(identificador: '', pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: FiltroCliente::TAMANHO_PAGINA_PADRAO);

        expect($filtro->identificador)->toBe('');
    });

    it('cria o filtro quando o identificador tem quantidade de caracteres dentro do limite permitido', function () {
        $filtro = new FiltroCliente(identificador: 'Artur Alves', pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: FiltroCliente::TAMANHO_PAGINA_PADRAO);

        expect($filtro->identificador)->toBe('Artur Alves');
    });

    it('lança exceção quando o identificador tem menos caracteres que o mínimo permitido', function () {
        expect(function () {
            new FiltroCliente(identificador: 'ar', pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: FiltroCliente::TAMANHO_PAGINA_PADRAO);
        })->toThrow(new DominioException($this->mensagemErro, ['O identificador deve ter entre 3 e 100 caracteres.']));
    });

    it('lança exceção quando o identificador tem mais caracteres que o máximo permitido', function () {
        expect(function () {
            new FiltroCliente(identificador: str_repeat('a', 101), pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: FiltroCliente::TAMANHO_PAGINA_PADRAO);
        })->toThrow(new DominioException($this->mensagemErro, ['O identificador deve ter entre 3 e 100 caracteres.']));
    });

    it('cria o filtro quando a página com valor dentro do limite permitido', function () {
        $filtro = new FiltroCliente(identificador: '', pagina: 1, tamanhoPagina: FiltroCliente::TAMANHO_PAGINA_PADRAO);

        expect($filtro->pagina)->toBe(1);
    });

    it('lança exceção quando a página é menor que o mínimo permitido', function () {
        expect(function () {
            new FiltroCliente(identificador: '', pagina: 0, tamanhoPagina: FiltroCliente::TAMANHO_PAGINA_PADRAO);
        })->toThrow(new DominioException($this->mensagemErro, ['A página deve ser maior ou igual a 1.']));
    });

    it('cria o filtro quando o tamanho da página com valor dentro do limite permitido', function () {
        $filtro = new FiltroCliente(identificador: '', pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: 10);

        expect($filtro->tamanhoPagina)->toBe(10);
    });

    it('lança exceção quando o tamanho da página é menor que o mínimo permitido', function () {
        expect(function () {
            new FiltroCliente(identificador: '', pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: 0);
        })->toThrow(new DominioException($this->mensagemErro, ['O tamanho da página deve ser maior ou igual a 10.']));
    });

    it('lança exceção quando o tamanho da página ultrapassa o máximo permitido', function () {
        expect(function () {
            new FiltroCliente(identificador: '', pagina: FiltroCliente::PAGINA_PADRAO, tamanhoPagina: 101);
        })->toThrow(new DominioException($this->mensagemErro, ['O tamanho da página deve ser menor ou igual a 100.']));
    });

    it('cria o filtro quando todos os campos são válidos', function () {
        $filtro = new FiltroCliente(
            identificador: 'Artur Alves',
            pagina: 2,
            tamanhoPagina: 25,
        );

        expect($filtro->identificador)->toBe('Artur Alves');
        expect($filtro->pagina)->toBe(2);
        expect($filtro->tamanhoPagina)->toBe(25);
    });

});
