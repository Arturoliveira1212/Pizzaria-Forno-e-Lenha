<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaIngredienteAddEstoque extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente ADD estoque INT NOT NULL AFTER nome;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE ingrediente DROP estoque');
    }
}
