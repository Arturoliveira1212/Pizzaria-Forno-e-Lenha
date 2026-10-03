<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaPizza extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE pizza (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL,
                descricao TEXT NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE pizza');
    }
}
