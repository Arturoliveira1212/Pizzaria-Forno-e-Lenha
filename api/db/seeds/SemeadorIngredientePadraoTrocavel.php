<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredientePadraoTrocavel extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorIngredientePadrao',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_padrao_trocavel (ingredientePadraoId, ingredienteId) VALUES
                (2, 12), (2, 13),
                (5, 12), (5, 13),
                (6, 18), (6, 15),
                (9, 12), (9, 13),
                (12, 4), (12, 18),
                (16, 12),
                (17, 18),
                (22, 12),
                (23, 13), (23, 14),
                (27, 12),
                (28, 4), (28, 18),
                (30, 12),
                (35, 12),
                (42, 12),
                (43, 4), (43, 9);
        SQL;
        $this->execute($sql);
    }
}
