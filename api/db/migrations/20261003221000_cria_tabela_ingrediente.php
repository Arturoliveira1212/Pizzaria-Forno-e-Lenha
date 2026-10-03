<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngrediente extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente');
    }
}
