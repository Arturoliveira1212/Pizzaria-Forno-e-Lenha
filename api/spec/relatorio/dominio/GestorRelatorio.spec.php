<?php

use Kahlan\Plugin\Double;

describe('GestorRelatorio', function () {

    beforeEach(function () {
        $this->sessao = new SessaoEmMemoria();
        $this->sessao->definir('idUsuario', 1);
        $this->sessao->definir('papel', PapelUsuario::ADMINISTRADOR->value);

        $this->repositorioRelatorio = Double::instance(['implements' => RepositorioRelatorio::class]);

        $this->gestorRelatorio = new GestorRelatorio(
            autorizacao: new Autorizacao($this->sessao),
            repositorioRelatorio: $this->repositorioRelatorio,
        );
    });

    describe('obterDistribuicaoItens()', function () {

        it('lança exceção quando o usuário logado não é administrador', function () {
            $this->sessao->definir('papel', PapelUsuario::ATENDENTE->value);

            /** @var SemAcessoException $e */
            $e = capturarExcecao(function () {
                $this->gestorRelatorio->obterDistribuicaoItens(new PeriodoParaRelatorio('2026-11-01', '2026-11-30'));
            });

            expect($e)->toBeAnInstanceOf(SemAcessoException::class);
        });

        it('retorna o percentual de cada item sobre o total vendido no período', function () {
            allow($this->repositorioRelatorio)->toReceive('obterValorVendidoPorItem')->andReturn([
                new ValorVendidoItem(nome: 'Pizza Calabresa', valor: new Dinheiro(7500)),
                new ValorVendidoItem(nome: 'Coca-Cola 2L', valor: new Dinheiro(2500)),
            ]);

            $distribuicaoItens = $this->gestorRelatorio->obterDistribuicaoItens(new PeriodoParaRelatorio('2026-11-01', '2026-11-30'));

            expect($distribuicaoItens[0]->percentual)->toBe(75.0);
            expect($distribuicaoItens[1]->percentual)->toBe(25.0);
        });

    });

});
