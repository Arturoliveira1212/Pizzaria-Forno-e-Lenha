<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaIngredienteAdicionalAddQuantidade extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente_adicional ADD quantidade INT NOT NULL AFTER preco;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE ingrediente_adicional DROP quantidade');
    }
}
