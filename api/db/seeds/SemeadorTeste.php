<?php

use Phinx\Seed\AbstractSeed;

class SemeadorTeste extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO teste (nome) VALUES ('Teste 1'), ('Teste 2'), ('Teste 3');
        SQL;
        $this->execute($sql);
    }
}