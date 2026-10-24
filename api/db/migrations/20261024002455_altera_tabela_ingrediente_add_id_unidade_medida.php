<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaIngredienteAddIdUnidadeMedida extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente ADD idUnidadeMedida INT NOT NULL AFTER estoque;
            ALTER TABLE ingrediente ADD CONSTRAINT fk_ingrediente_unidade_medida
                FOREIGN KEY (idUnidadeMedida) REFERENCES unidade_medida(id)
                ON DELETE RESTRICT ON UPDATE CASCADE;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente DROP FOREIGN KEY fk_ingrediente_unidade_medida;
            ALTER TABLE ingrediente DROP idUnidadeMedida;
        SQL;
        $this->execute($sql);
    }
}
