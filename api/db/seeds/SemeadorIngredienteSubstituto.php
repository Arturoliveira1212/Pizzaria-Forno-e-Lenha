<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredienteSubstituto extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorIngrediente',
            'SemeadorPizza',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_substituto (idIngrediente, idPizza) VALUES
                (15, 1), (12, 1), (12, 2), (15, 2), (12, 3),
                (15, 3), (15, 4), (18, 4), (12, 5), (14, 5),
                (15, 6), (18, 6), (12, 7), (13, 7), (14, 8),
                (15, 8), (12, 9), (14, 9), (15, 10), (13, 10);
        SQL;
        $this->execute($sql);
    }
}
