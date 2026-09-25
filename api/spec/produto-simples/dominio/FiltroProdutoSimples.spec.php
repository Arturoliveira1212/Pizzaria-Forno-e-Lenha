<?php

describe('FiltroProdutoSimples', function () {

    $this->mensagemErro = 'Erro ao criar filtro para produtos simples.';
    $this->paginacaoValida = new Paginacao();

    it('cria o filtro quando o identificador é vazio, representando "sem filtro" (todos os produtos simples)', function () {
        $filtro = new FiltroProdutoSimples(tipoEmString: null, identificador: '', paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe('');
    });

    it('cria o filtro quando o identificador tem quantidade de caracteres dentro do permitido', function () {
        $filtro = new FiltroProdutoSimples(tipoEmString: null, identificador: 'Artur Alves', paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe('Artur Alves');
    });

    it('lança exceção quando o identificador tem mais caracteres que o máximo permitido', function () {
        expect(function () {
            new FiltroProdutoSimples(tipoEmString: null, identificador: str_repeat('a', 101), paginacao: $this->paginacaoValida);
        })->toThrow(new DominioException($this->mensagemErro, ['O identificador deve ter no máximo ' . FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.']));
    });

    it('cria o filtro quando o identificador tem exatamente o tamanho máximo permitido', function () {
        $filtro = new FiltroProdutoSimples(tipoEmString: null, identificador: str_repeat('a', FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA), paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe(str_repeat('a', FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA));
    });

    it('cria o filtro com tipo nulo quando nenhum tipo foi informado', function () {
        $filtro = new FiltroProdutoSimples(tipoEmString: null, identificador: '', paginacao: $this->paginacaoValida);

        expect($filtro->tipo)->toBe(null);
    });

    it('cria o filtro com o tipo correto quando é informada a string correspondente ao enum', function () {
        $tipoProdutoBebida = TipoProdutoSimples::BEBIDA;

        $filtro = new FiltroProdutoSimples(tipoEmString: $tipoProdutoBebida->value, identificador: '', paginacao: $this->paginacaoValida);

        expect($filtro->tipo)->toBe($tipoProdutoBebida);
    });

    it('lança exceção quando o tipo informado não existe no enum', function () {
        expect(function () {
            new FiltroProdutoSimples(tipoEmString: 'tipo_que_nao_existe', identificador: '', paginacao: $this->paginacaoValida);
        })->toThrow(new DominioException($this->mensagemErro, ['Tipo de produto inválido.']));
    });

    it('lança exceção quando o tipo é uma string vazia', function () {
        expect(function () {
            new FiltroProdutoSimples(tipoEmString: '', identificador: '', paginacao: $this->paginacaoValida);
        })->toThrow(new DominioException($this->mensagemErro, ['Tipo de produto inválido.']));
    });

    it('cria o filtro quando todos os campos são válidos', function () {
        $tipoProdutoBebida = TipoProdutoSimples::BEBIDA;

        $filtro = new FiltroProdutoSimples(tipoEmString: $tipoProdutoBebida->value, identificador: 'Artur Alves', paginacao: $this->paginacaoValida);

        expect($filtro->identificador)->toBe('Artur Alves');
        expect($filtro->tipo)->toBe($tipoProdutoBebida);
        expect($filtro->paginacao)->toBe($this->paginacaoValida);
    });

});
