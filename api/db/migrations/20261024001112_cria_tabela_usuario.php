<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaUsuario extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE usuario (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(150) NOT NULL,
                cpf VARCHAR(11) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                papel ENUM('ADMINISTRADOR', 'ATENDENTE') NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE usuario');
    }
}
