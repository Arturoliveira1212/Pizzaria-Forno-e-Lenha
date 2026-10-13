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
            INSERT INTO ingrediente_adicional (idIngrediente, preco) VALUES
                (4, 500), (6, 600), (7, 500), (11, 450),
                (12, 550), (15, 650), (16, 400), (17, 350), (18, 600);
        SQL;
        $this->execute($sql);
    }
}
