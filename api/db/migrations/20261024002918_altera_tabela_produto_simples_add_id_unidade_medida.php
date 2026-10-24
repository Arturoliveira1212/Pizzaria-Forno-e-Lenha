<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaProdutoSimplesAddIdUnidadeMedida extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE produto_simples ADD idUnidadeMedida INT NOT NULL AFTER estoque;
            ALTER TABLE produto_simples ADD CONSTRAINT fk_produto_simples_unidade_medida
                    FOREIGN KEY (idUnidadeMedida) REFERENCES unidade_medida(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $sql = <<<'SQL'
            ALTER TABLE produto_simples DROP FOREIGN KEY fk_produto_simples_unidade_medida;
            ALTER TABLE produto_simples DROP idUnidadeMedida;
        SQL;
        $this->execute($sql);
    }
}
