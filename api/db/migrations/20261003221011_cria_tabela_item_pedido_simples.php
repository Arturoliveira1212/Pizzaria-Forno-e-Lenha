<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaItemPedidoSimples extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE item_pedido_simples (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idPedido INT NOT NULL,
                quantidade INT NOT NULL,
                idProdutoSimples INT NOT NULL,
                precoPraticado INT NOT NULL DEFAULT 0,
                CONSTRAINT fk_item_pedido_simples_pedido
                    FOREIGN KEY (idPedido) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_item_pedido_simples_produto
                    FOREIGN KEY (idProdutoSimples) REFERENCES produto_simples(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE item_pedido_simples');
    }
}
