<?php

use Phinx\Seed\AbstractSeed;

class $className extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL

        SQL;
        $this->execute($sql);
    }
}