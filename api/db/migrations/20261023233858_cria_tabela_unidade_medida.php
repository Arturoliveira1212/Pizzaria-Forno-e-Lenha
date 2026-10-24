<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaUnidadeMedida extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE unidade_medida (
                id INT PRIMARY KEY AUTO_INCREMENT,
                descricao VARCHAR(50) NOT NULL,
                sigla VARCHAR(10) NOT NULL,
                UNIQUE KEY uq_unidade_medida_descricao (descricao),
                UNIQUE KEY uq_unidade_medida_sigla (sigla)
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE unidade_medida');
    }
}
