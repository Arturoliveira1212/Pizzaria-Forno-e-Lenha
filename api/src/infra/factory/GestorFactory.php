<?php

class GestorFactory {
    public static function criarGestorPedido(BancoDados $bancoDados): GestorPedido {
        $repositorioCliente = new RepositorioClienteEmBDR($bancoDados);
        $repositorioPizza = new RepositorioPizzaEmBDR($bancoDados);
        $repositorioUsuario = new RepositorioUsuarioEmBDR();
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
        );
    }
}
