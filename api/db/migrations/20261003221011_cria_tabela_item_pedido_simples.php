<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaItemPedidoSimples extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE item_pedido_simples (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pedidoId INT NOT NULL,
                quantidade INT NOT NULL,
                produtoSimplesId INT NOT NULL,
                CONSTRAINT fk_item_pedido_simples_pedido
                    FOREIGN KEY (pedidoId) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_item_pedido_simples_produto
                    FOREIGN KEY (produtoSimplesId) REFERENCES produto_simples(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE item_pedido_simples');
    }
}
