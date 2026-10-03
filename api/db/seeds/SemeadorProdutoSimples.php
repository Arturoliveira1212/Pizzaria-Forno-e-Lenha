<?php

use Phinx\Seed\AbstractSeed;

class SemeadorProdutoSimples extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO produto_simples (nome, descricao, preco, tipo) VALUES
                ('Coca-Cola 2L', 'Refrigerante Coca-Cola, garrafa de 2 litros', 1200, 'BEBIDA'),
                ('Guaraná Antarctica 2L', 'Refrigerante Guaraná Antarctica, garrafa de 2 litros', 1100, 'BEBIDA'),
                ('Água Mineral 500ml', 'Água mineral sem gás, garrafa de 500ml', 400, 'BEBIDA'),
                ('Petit Gateau', 'Bolo de chocolate quente com recheio cremoso e sorvete de creme', 1800, 'SOBREMESA'),
                ('Pudim de Leite', 'Pudim de leite condensado tradicional com calda de caramelo', 1500, 'SOBREMESA');
        SQL;
        $this->execute($sql);
    }
}
