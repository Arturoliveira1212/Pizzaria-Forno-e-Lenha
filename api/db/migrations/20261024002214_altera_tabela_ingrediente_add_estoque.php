<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaIngredienteAddEstoque extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente ADD estoque INT UNSIGNED NOT NULL DEFAULT 0 AFTER nome;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE ingrediente DROP estoque');
    }
}
