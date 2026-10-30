<?php

use Kahlan\Plugin\Double;

describe('GestorAutenticacao', function () {

    beforeEach(function () {
        $geradorHashSenha = new GeradorHashSenha('pimenta-de-teste');

        $this->usuario = new Usuario(
            id: 2,
            nome: 'Mariana Silva',
            cpf: new Cpf('98765432100'),
            senha: $geradorHashSenha->gerar('atendente123'),
            papel: PapelUsuario::ATENDENTE,
        );

        $this->repositorioUsuario = Double::instance(['implements' => RepositorioUsuario::class]);
        allow($this->repositorioUsuario)->toReceive('obterUsuarioPorCpf')->andReturn($this->usuario);

        $this->sessao = new SessaoEmMemoria();

        $this->gestorAutenticacao = new GestorAutenticacao(
            repositorioUsuario: $this->repositorioUsuario,
            sessao: $this->sessao,
            geradorHashSenha: $geradorHashSenha,
        );
    });

    describe('entrar()', function () {

        it('lança exceção quando a senha está incorreta', function () {
            /** @var AutenticacaoException $e */
            $e = capturarExcecao(function () {
                $this->gestorAutenticacao->entrar('987.654.321-00', 'senha-errada', '127.0.0.1', 'Navegador');
            });

            expect($e)->toBeAnInstanceOf(AutenticacaoException::class);
            expect($e->getMessage())->toBe('CPF ou senha incorretos.');
            expect($this->sessao->dados)->toBe([]);
        });

        it('retorna o usuário e guarda o usuário, o IP e o navegador na sessão quando as credenciais estão corretas', function () {
            $usuario = $this->gestorAutenticacao->entrar('987.654.321-00', 'atendente123', '127.0.0.1', 'Navegador');

            expect($usuario)->toBe($this->usuario);
            expect($this->sessao->dados['idUsuario'])->toBe(2);
            expect($this->sessao->dados['ip'])->toBe('127.0.0.1');
            expect($this->sessao->dados['navegador'])->toBe('Navegador');
        });

    });

    describe('validarSessao()', function () {

        it('destrói a sessão e lança exceção quando o navegador é diferente do usado no login', function () {
            $this->gestorAutenticacao->entrar('98765432100', 'atendente123', '127.0.0.1', 'Navegador');

            /** @var AutenticacaoException $e */
            $e = capturarExcecao(function () {
                $this->gestorAutenticacao->validarSessao('127.0.0.1', 'Outro navegador');
            });

            expect($e)->toBeAnInstanceOf(AutenticacaoException::class);
            expect($this->sessao->destruida)->toBe(true);
        });

    });

});
