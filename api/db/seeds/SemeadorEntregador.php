<?php

use Phinx\Seed\AbstractSeed;

class SemeadorEntregador extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO entregador (nome) VALUES
                ('João Pedro Alves'),
                ('Marcos Vinícius Rocha'),
                ('Juliana Ferreira Dias');
        SQL;
        $this->execute($sql);
    }
}
