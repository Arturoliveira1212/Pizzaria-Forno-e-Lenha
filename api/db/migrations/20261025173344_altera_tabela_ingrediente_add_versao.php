<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaIngredienteAddVersao extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente ADD versao INT NOT NULL DEFAULT 1 AFTER idUnidadeMedida;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE ingrediente DROP versao');
    }
}
