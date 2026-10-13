<?php

use Phinx\Seed\AbstractSeed;

class SemeadorAdicionalPizza extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorIngredienteAdicional',
            'SemeadorPizza',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO adicional_pizza (idIngredienteAdicional, idPizza) VALUES
                (1, 1), (2, 1), (3, 1), (4, 2), (5, 2), (6, 2),
                (1, 3), (3, 3), (4, 4), (5, 4), (7, 4),
                (1, 5), (3, 5), (8, 5), (2, 6), (5, 6),
                (6, 7), (7, 7), (8, 8), (9, 8), (1, 9),
                (3, 9), (4, 10), (9, 10);
        SQL;
        $this->execute($sql);
    }
}
