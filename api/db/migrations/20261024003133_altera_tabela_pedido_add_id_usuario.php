<?php

use Phinx\Migration\AbstractMigration;

final class AlteraTabelaPedidoAddIdUsuario extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            ALTER TABLE pedido ADD idUsuario INT NOT NULL AFTER id;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('ALTER TABLE pedido DROP idUsuario');
    }
}
