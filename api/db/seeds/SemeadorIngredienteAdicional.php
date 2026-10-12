<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredienteAdicional extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorPizza',
            'SemeadorIngrediente',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_adicional (idPizza, idIngrediente, preco) VALUES
                (1, 18, 500), (1, 7, 400), (1, 11, 450),
                (2, 18, 500), (2, 7, 400), (2, 11, 450),
                (3, 18, 500), (3, 7, 400), (3, 11, 450),
                (4, 18, 500), (4, 7, 400), (4, 11, 450),
                (5, 18, 500), (5, 7, 400), (5, 11, 450),
                (6, 18, 500), (6, 7, 400), (6, 11, 450),
                (7, 18, 500), (7, 7, 400), (7, 11, 450),
                (8, 18, 500), (8, 7, 400), (8, 11, 450),
                (9, 18, 500), (9, 7, 400), (9, 11, 450),
                (10, 18, 500), (10, 7, 400), (10, 11, 450);
        SQL;
        $this->execute($sql);
    }
}
