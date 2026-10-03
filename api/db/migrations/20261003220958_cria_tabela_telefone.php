<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaTelefone extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE telefone (
                id INT PRIMARY KEY AUTO_INCREMENT,
                clienteId INT NOT NULL,
                ddd VARCHAR(3) NOT NULL,
                numero VARCHAR(20) NOT NULL,
                UNIQUE KEY uq_telefone_cliente (clienteId),
                CONSTRAINT fk_telefone_cliente
                    FOREIGN KEY (clienteId) REFERENCES cliente(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE telefone');
    }
}
