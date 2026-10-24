<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaIngredienteAdicionalAddIdUnidadeMedida extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente_adicional ADD idUnidadeMedida INT NOT NULL AFTER quantidade;
            ALTER TABLE ingrediente_adicional ADD CONSTRAINT fk_ingrediente_adicional_unidade_medida
                FOREIGN KEY (idUnidadeMedida) REFERENCES unidade_medida(id)
                ON DELETE RESTRICT ON UPDATE CASCADE;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $sql = <<<'SQL'
            ALTER TABLE ingrediente_adicional DROP FOREIGN KEY fk_ingrediente_adicional_unidade_medida;
            ALTER TABLE ingrediente_adicional DROP idUnidadeMedida;
        SQL;
        $this->execute($sql);
    }
}
