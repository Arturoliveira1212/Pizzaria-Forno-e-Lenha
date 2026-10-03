<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaSaborEscolhido extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE sabor_escolhido (
                id INT PRIMARY KEY AUTO_INCREMENT,
                itemPedidoPizzaId INT NOT NULL,
                pizzaId INT NOT NULL,
                CONSTRAINT fk_sabor_escolhido_item
                    FOREIGN KEY (itemPedidoPizzaId) REFERENCES item_pedido_pizza(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_sabor_escolhido_pizza
                    FOREIGN KEY (pizzaId) REFERENCES pizza(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE sabor_escolhido');
    }
}
