<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaEntregadorAddDisponivel extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE entregador ADD disponivel BOOLEAN NOT NULL DEFAULT TRUE AFTER numero;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE entregador DROP disponivel');
    }
}
