<?php

use $useClassName;

final class $className extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'

        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('');
    }
}