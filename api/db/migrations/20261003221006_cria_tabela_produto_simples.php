<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaProdutoSimples extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE produto_simples (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL,
                descricao TEXT NOT NULL,
                preco INT NOT NULL,
                tipo ENUM('BEBIDA', 'SOBREMESA') NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE produto_simples');
    }
}
