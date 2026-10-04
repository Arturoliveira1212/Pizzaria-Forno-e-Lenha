<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaCliente extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE cliente (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(150) NOT NULL,
                cpf VARCHAR(11) NOT NULL UNIQUE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE cliente');
    }
}
