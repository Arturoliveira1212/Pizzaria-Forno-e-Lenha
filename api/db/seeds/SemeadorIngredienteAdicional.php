<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredienteAdicional extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorPizza',
            'SemeadorIngrediente',
            'SemeadorUnidadeMedida',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_adicional (idIngrediente, preco, quantidade, idUnidadeMedida) VALUES
                (4, 500, 100, 1), (6, 600, 100, 1), (7, 500, 100, 1),
                (11, 450, 50, 1), (12, 550, 100, 1), (15, 650, 100, 1),
                (16, 400, 100, 1), (17, 350, 100, 1), (18, 600, 100, 1);
        SQL;
        $this->execute($sql);
    }
}
