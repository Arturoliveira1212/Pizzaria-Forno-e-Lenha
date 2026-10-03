<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaCliente extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE cliente (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(150) NOT NULL,
                enderecoId INT NULL,
                CONSTRAINT fk_cliente_endereco
                    FOREIGN KEY (enderecoId) REFERENCES endereco(id)
                    ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE cliente');
    }
}
