<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaEntregador extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE entregador (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(150) NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE entregador');
    }
}
