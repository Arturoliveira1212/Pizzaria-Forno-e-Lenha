<?php

class GestorFactory {
    public static function criarGestorPedido(BancoDados $bancoDados, ?ProvedorUsuarioLogado $provedorUsuarioLogado = null): GestorPedido {
        $repositorioCliente = new RepositorioClienteEmBDR($bancoDados);
        $repositorioPizza = new RepositorioPizzaEmBDR($bancoDados);
        $repositorioUsuario = new RepositorioUsuarioEmBDR($bancoDados);
        $repositorioEntregador = new RepositorioEntregadorEmBDR($bancoDados);
        $repositorioPedido = new RepositorioPedidoEmBDR(
            bancoDados: $bancoDados,
            repositorioPizza: $repositorioPizza,
            repositorioCliente: $repositorioCliente,
            repositorioUsuario: $repositorioUsuario,
            repositorioEntregador: $repositorioEntregador,
        );

        return new GestorPedido(
            unidadeTrabalho: new UnidadeTrabalhoEmBDR($bancoDados),
            repositorioPedido: $repositorioPedido,
            repositorioCliente: $repositorioCliente,
            repositorioPizza: $repositorioPizza,
            repositorioProdutoSimples: new RepositorioProdutoSimplesEmBDR($bancoDados),
            repositorioIngrediente: new RepositorioIngredienteEmBDR($bancoDados),
            repositorioPromocao: new RepositorioPromocaoEmBDR($bancoDados),
            repositorioConfiguracaoFidelidade: new RepositorioConfiguracaoFidelidadeEmBDR($bancoDados),
            repositorioMovimentacaoEstoque: new RepositorioMovimentacaoEstoqueEmBDR($bancoDados),
            repositorioEntregador: $repositorioEntregador,
            provedorUsuarioLogado: $provedorUsuarioLogado ?? self::criarGestorAutenticacao($bancoDados),
        );
    }

    public static function criarGestorAutenticacao(BancoDados $bancoDados): GestorAutenticacao {
        return new GestorAutenticacao(
            repositorioUsuario: new RepositorioUsuarioEmBDR($bancoDados),
            sessao: new SessaoPHP(),
            geradorHashSenha: new GeradorHashSenha($_ENV['PIMENTA']),
        );
    }

    public static function criarGestorRelatorio(BancoDados $bancoDados): GestorRelatorio {
        return new GestorRelatorio(
            autorizacao: new Autorizacao(new SessaoPHP()),
            repositorioRelatorio: new RepositorioRelatorioEmBDR($bancoDados),
        );
    }
}
