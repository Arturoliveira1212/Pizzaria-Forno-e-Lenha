<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaItemPedidoPizza extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE item_pedido_pizza (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pedidoId INT NOT NULL,
                quantidade INT NOT NULL,
                precoPraticado INT NOT NULL DEFAULT 0,
                tamanho ENUM('PEQUENA', 'MEDIA', 'GRANDE') NOT NULL,
                CONSTRAINT fk_item_pedido_pizza_pedido
                    FOREIGN KEY (pedidoId) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE item_pedido_pizza');
    }
}
