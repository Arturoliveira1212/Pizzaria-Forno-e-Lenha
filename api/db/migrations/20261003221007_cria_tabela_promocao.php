<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaPromocao extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE promocao (
                id INT PRIMARY KEY AUTO_INCREMENT,
                descricao TEXT NOT NULL,
                percentualDesconto INT NOT NULL,
                quantidadeMinima INT NOT NULL,
                dataInicio DATE NOT NULL,
                validade DATE NOT NULL,
                tamanho ENUM('PEQUENA', 'MEDIA', 'GRANDE') NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE promocao');
    }
}
