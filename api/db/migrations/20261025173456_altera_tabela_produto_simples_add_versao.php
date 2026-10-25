<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaProdutoSimplesAddVersao extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE produto_simples ADD versao INT NOT NULL DEFAULT 1 AFTER idUnidadeMedida;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE produto_simples DROP versao');
    }
}
