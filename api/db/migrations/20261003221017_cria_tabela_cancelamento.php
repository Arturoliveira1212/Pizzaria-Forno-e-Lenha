<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaCancelamento extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE cancelamento (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pedidoId INT NOT NULL,
                motivo TEXT NOT NULL,
                data DATETIME NOT NULL,
                UNIQUE KEY uq_cancelamento_pedido (pedidoId),
                CONSTRAINT fk_cancelamento_pedido
                    FOREIGN KEY (pedidoId) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE cancelamento');
    }
}
