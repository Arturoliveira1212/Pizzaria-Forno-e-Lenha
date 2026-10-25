<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaProdutoSimplesAddEstoque extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE produto_simples ADD estoque INT UNSIGNED NOT NULL DEFAULT 0 AFTER tipo;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE produto_simples DROP estoque');
    }
}
