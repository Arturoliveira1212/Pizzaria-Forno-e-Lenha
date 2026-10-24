<?php

use Phinx\Seed\AbstractSeed;

class SemeadorProdutoSimples extends AbstractSeed {
    public function getDependencies(): array {
        return ['SemeadorUnidadeMedida'];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO produto_simples (nome, descricao, preco, tipo, estoque, idUnidadeMedida) VALUES
                ('Coca-Cola 2L', 'Refrigerante Coca-Cola, garrafa de 2 litros', 1200, 'BEBIDA', 30, 2),
                ('Guaraná Antarctica 2L', 'Refrigerante Guaraná Antarctica, garrafa de 2 litros', 1100, 'BEBIDA', 25, 2),
                ('Água Mineral 500ml', 'Água mineral sem gás, garrafa de 500ml', 400, 'BEBIDA', 50, 2),
                ('Petit Gateau', 'Bolo de chocolate quente com recheio cremoso e sorvete de creme', 1800, 'SOBREMESA', 15, 2),
                ('Pudim de Leite', 'Pudim de leite condensado tradicional com calda de caramelo', 1500, 'SOBREMESA', 20, 2);
        SQL;
        $this->execute($sql);
    }
}
