<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredientePadrao extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorPizza',
            'SemeadorIngrediente',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_padrao (pizzaId, ingredienteId, removivel, substituivel) VALUES
                (1, 1, FALSE, FALSE), (1, 2, TRUE, TRUE), (1, 3, TRUE, TRUE),
                (2, 1, FALSE, FALSE), (2, 2, TRUE, TRUE), (2, 4, TRUE, TRUE), (2, 5, TRUE, TRUE),
                (3, 1, FALSE, FALSE), (3, 2, TRUE, TRUE), (3, 19, TRUE, TRUE),
                (4, 1, FALSE, FALSE), (4, 6, TRUE, TRUE), (4, 7, TRUE, TRUE), (4, 8, TRUE, TRUE),
                (5, 1, FALSE, FALSE), (5, 2, TRUE, TRUE), (5, 9, TRUE, TRUE), (5, 10, TRUE, TRUE), (5, 5, TRUE, TRUE), (5, 11, TRUE, TRUE),
                (6, 1, FALSE, FALSE), (6, 2, TRUE, TRUE), (6, 12, TRUE, TRUE), (6, 13, TRUE, TRUE), (6, 14, TRUE, TRUE),
                (7, 1, FALSE, FALSE), (7, 2, TRUE, TRUE), (7, 15, TRUE, TRUE),
                (8, 1, FALSE, FALSE), (8, 2, TRUE, TRUE), (8, 16, TRUE, TRUE), (8, 13, TRUE, TRUE), (8, 3, TRUE, TRUE),
                (9, 1, FALSE, FALSE), (9, 2, TRUE, TRUE), (9, 17, TRUE, TRUE), (9, 5, TRUE, TRUE), (9, 8, TRUE, TRUE), (9, 11, TRUE, TRUE), (9, 16, TRUE, TRUE),
                (10, 1, FALSE, FALSE), (10, 2, TRUE, TRUE), (10, 18, TRUE, TRUE), (10, 5, TRUE, TRUE);
        SQL;
        $this->execute($sql);
    }
}
