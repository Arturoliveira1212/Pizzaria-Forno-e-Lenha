<?php

describe('FiltroProdutoSimples', function () {

    describe('validar()', function () {

        $this->mensagemErro = 'Erro ao criar filtro para produtos simples.';
        $this->paginacaoValida = new Paginacao();

        it('cria o filtro quando o identificador é vazio, representando "sem filtro" (todos os produtos simples)', function () {
            $filtro = new FiltroProdutoSimples(tipoEmString: null, identificador: '', paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe('');
        });

        it('lança exceção quando o identificador tem mais caracteres que o máximo permitido', function () {
            expect(function () {
                $identificador = str_repeat('a', FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA + 1);
                new FiltroProdutoSimples(tipoEmString: null, identificador: $identificador, paginacao: $this->paginacaoValida);
            })->toThrow(new DominioException('O identificador deve ter no máximo ' . FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.'));
        });

        it('lança exceção quando o tipo informado não existe no enum', function () {
            expect(function () {
                new FiltroProdutoSimples(tipoEmString: 'tipo_que_nao_existe', identificador: '', paginacao: $this->paginacaoValida);
            })->toThrow(new DominioException('Tipo de produto inválido.'));
        });

        it('lança exceção quando o tipo é uma string vazia', function () {
            expect(function () {
                new FiltroProdutoSimples(tipoEmString: '', identificador: '', paginacao: $this->paginacaoValida);
            })->toThrow(new DominioException('Tipo de produto inválido.'));
        });

        it('cria o filtro com tipo nulo quando nenhum tipo foi informado', function () {
            $identificador = str_repeat('a', FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA);
            $filtro = new FiltroProdutoSimples(tipoEmString: null, identificador: $identificador, paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe($identificador);
            expect($filtro->tipo)->toBe(null);
            expect($filtro->paginacao)->toBe($this->paginacaoValida);
        });

        it('cria o filtro quando todos os campos são válidos', function () {
            $tipoProdutoBebida = TipoProdutoSimples::BEBIDA;
            $identificador = str_repeat('a', FiltroProdutoSimples::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA);
            $filtro = new FiltroProdutoSimples(tipoEmString: $tipoProdutoBebida->value, identificador: $identificador, paginacao: $this->paginacaoValida);

            expect($filtro->identificador)->toBe($identificador);
            expect($filtro->tipo)->toBe($tipoProdutoBebida);
            expect($filtro->paginacao)->toBe($this->paginacaoValida);
        });

    });

});
