<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaEndereco extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE endereco (
                id INT PRIMARY KEY AUTO_INCREMENT,
                logradouro VARCHAR(255) NOT NULL,
                bairro VARCHAR(100) NOT NULL,
                cidade VARCHAR(100) NOT NULL,
                uf CHAR(2) NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE endereco');
    }
}
