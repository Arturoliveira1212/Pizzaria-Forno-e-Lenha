<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaUsuarioModifySenha extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE usuario MODIFY senha VARCHAR(100) NOT NULL;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE usuario MODIFY senha VARCHAR(255) NOT NULL');
    }
}
