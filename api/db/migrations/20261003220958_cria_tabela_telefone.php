<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaTelefone extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE telefone (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idCliente INT NOT NULL,
                ddd VARCHAR(3) NOT NULL,
                numero VARCHAR(20) NOT NULL,
                UNIQUE KEY uq_telefone_cliente (idCliente),
                CONSTRAINT fk_telefone_cliente
                    FOREIGN KEY (idCliente) REFERENCES cliente(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE telefone');
    }
}
