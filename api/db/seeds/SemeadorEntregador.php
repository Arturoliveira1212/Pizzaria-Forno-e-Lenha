<?php

use Phinx\Seed\AbstractSeed;

class SemeadorEntregador extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO entregador (nome, ddd, numero, disponivel) VALUES
                ('João Pedro Alves', '21', '987654321', 1),
                ('Marcos Vinícius Rocha', '21', '976543210', 1),
                ('Juliana Ferreira Dias', '22', '965432109', 0);
        SQL;
        $this->execute($sql);
    }
}
