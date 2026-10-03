<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaConfiguracaoFidelidade extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE configuracao_fidelidade (
                id INT PRIMARY KEY AUTO_INCREMENT,
                valorMinimo INT NOT NULL,
                percentualDesconto INT NOT NULL,
                validadeEmDias INT NOT NULL
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE configuracao_fidelidade');
    }
}
