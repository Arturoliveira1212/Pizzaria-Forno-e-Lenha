<?php

class ProdutoSimplesFactory {
    public static function cocaCola(int $estoque = 1): ProdutoSimples {
        return new ProdutoSimples(
            id: 1,
            nome: 'Coca-Cola',
            descricao: 'Refrigerante de cola',
            tipo: TipoProdutoSimples::BEBIDA,
            preco: new Dinheiro(590),
            estoque: new Quantidade($estoque, UnidadeMedidaFactory::litros()),
        );
    }

    public static function sorvete(): ProdutoSimples {
        return new ProdutoSimples(
            id: 2,
            nome: 'Sorvete',
            descricao: 'Sorvete de chocolate',
            tipo: TipoProdutoSimples::SOBREMESA,
            preco: new Dinheiro(799),
            estoque: new Quantidade(350, UnidadeMedidaFactory::gramas()),
        );
    }

    public static function sucoLaranja(): ProdutoSimples {
        return new ProdutoSimples(
            id: 3,
            nome: 'Suco de Laranja',
            descricao: 'Suco natural de laranja',
            tipo: TipoProdutoSimples::BEBIDA,
            preco: new Dinheiro(998),
            estoque: new Quantidade(1, UnidadeMedidaFactory::litros()),
        );
    }
}
