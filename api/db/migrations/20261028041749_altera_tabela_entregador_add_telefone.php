<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaEntregadorAddTelefone extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE entregador ADD ddd VARCHAR(3) NOT NULL AFTER nome;
            ALTER TABLE entregador ADD numero VARCHAR(20) NOT NULL AFTER ddd;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE entregador DROP numero, DROP ddd');
    }
}
