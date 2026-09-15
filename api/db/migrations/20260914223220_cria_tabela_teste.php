<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaTeste extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE teste (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(255)
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE teste');
    }
}