<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaPedido extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE pedido (
                id INT PRIMARY KEY AUTO_INCREMENT,
                data DATETIME NOT NULL,
                valorTotal INT NOT NULL DEFAULT 0,
                status ENUM(
                    'RECEBIDO',
                    'EM_PREPARACAO',
                    'SAIU_PARA_ENTREGA',
                    'PRONTO_PARA_RETIRADA',
                    'FINALIZADO',
                    'CANCELADO'
                ) NOT NULL DEFAULT 'RECEBIDO',
                tipoAtendimento ENUM('BALCAO', 'ENTREGA', 'RETIRADA') NOT NULL,
                clienteId INT NULL,
                CONSTRAINT fk_pedido_cliente
                    FOREIGN KEY (clienteId) REFERENCES cliente(id)
                    ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE pedido');
    }
}
