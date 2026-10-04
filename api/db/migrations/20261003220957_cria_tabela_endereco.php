<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaEndereco extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE endereco (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idCliente INT NOT NULL,
                logradouro VARCHAR(255) NOT NULL,
                numero VARCHAR(20) NOT NULL,
                bairro VARCHAR(100) NOT NULL,
                cidade VARCHAR(100) NOT NULL,
                estado VARCHAR(100) NOT NULL,
                uf CHAR(2) NOT NULL,
                CONSTRAINT fk_endereco_cliente
                    FOREIGN KEY (idCliente) REFERENCES cliente(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE endereco');
    }
}
