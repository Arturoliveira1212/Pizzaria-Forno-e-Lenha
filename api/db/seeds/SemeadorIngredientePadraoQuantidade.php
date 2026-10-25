<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredientePadraoQuantidade extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorIngredientePadrao',
            'SemeadorUnidadeMedida',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_padrao_quantidade
                (idIngredientePadrao, tamanho, quantidade, idUnidadeMedida) VALUES
                (1, 'PEQUENA', 50, 3), (1, 'MEDIA', 75, 3), (1, 'GRANDE', 100, 3),
                (2, 'PEQUENA', 100, 1), (2, 'MEDIA', 150, 1), (2, 'GRANDE', 200, 1),
                (3, 'PEQUENA', 2, 4), (3, 'MEDIA', 4, 4), (3, 'GRANDE', 6, 4),
                (4, 'PEQUENA', 50, 3), (4, 'MEDIA', 75, 3), (4, 'GRANDE', 100, 3),
                (5, 'PEQUENA', 100, 1), (5, 'MEDIA', 150, 1), (5, 'GRANDE', 200, 1),
                (6, 'PEQUENA', 100, 1), (6, 'MEDIA', 150, 1), (6, 'GRANDE', 200, 1),
                (7, 'PEQUENA', 100, 1), (7, 'MEDIA', 150, 1), (7, 'GRANDE', 200, 1),
                (8, 'PEQUENA', 50, 3), (8, 'MEDIA', 75, 3), (8, 'GRANDE', 100, 3),
                (9, 'PEQUENA', 100, 1), (9, 'MEDIA', 150, 1), (9, 'GRANDE', 200, 1),
                (10, 'PEQUENA', 2, 1), (10, 'MEDIA', 3, 1), (10, 'GRANDE', 5, 1),
                (11, 'PEQUENA', 50, 3), (11, 'MEDIA', 75, 3), (11, 'GRANDE', 100, 3),
                (12, 'PEQUENA', 100, 1), (12, 'MEDIA', 150, 1), (12, 'GRANDE', 200, 1),
                (13, 'PEQUENA', 100, 1), (13, 'MEDIA', 150, 1), (13, 'GRANDE', 200, 1),
                (14, 'PEQUENA', 100, 1), (14, 'MEDIA', 150, 1), (14, 'GRANDE', 200, 1),
                (15, 'PEQUENA', 50, 3), (15, 'MEDIA', 75, 3), (15, 'GRANDE', 100, 3),
                (16, 'PEQUENA', 100, 1), (16, 'MEDIA', 150, 1), (16, 'GRANDE', 200, 1),
                (17, 'PEQUENA', 100, 1), (17, 'MEDIA', 150, 1), (17, 'GRANDE', 200, 1),
                (18, 'PEQUENA', 2, 2), (18, 'MEDIA', 4, 2), (18, 'GRANDE', 6, 2),
                (19, 'PEQUENA', 100, 1), (19, 'MEDIA', 150, 1), (19, 'GRANDE', 200, 1),
                (20, 'PEQUENA', 100, 1), (20, 'MEDIA', 150, 1), (20, 'GRANDE', 200, 1),
                (21, 'PEQUENA', 50, 3), (21, 'MEDIA', 75, 3), (21, 'GRANDE', 100, 3),
                (22, 'PEQUENA', 100, 1), (22, 'MEDIA', 150, 1), (22, 'GRANDE', 200, 1),
                (23, 'PEQUENA', 100, 1), (23, 'MEDIA', 150, 1), (23, 'GRANDE', 200, 1),
                (24, 'PEQUENA', 100, 1), (24, 'MEDIA', 150, 1), (24, 'GRANDE', 200, 1),
                (25, 'PEQUENA', 100, 1), (25, 'MEDIA', 150, 1), (25, 'GRANDE', 200, 1),
                (26, 'PEQUENA', 50, 3), (26, 'MEDIA', 75, 3), (26, 'GRANDE', 100, 3),
                (27, 'PEQUENA', 100, 1), (27, 'MEDIA', 150, 1), (27, 'GRANDE', 200, 1),
                (28, 'PEQUENA', 100, 1), (28, 'MEDIA', 150, 1), (28, 'GRANDE', 200, 1),
                (29, 'PEQUENA', 50, 3), (29, 'MEDIA', 75, 3), (29, 'GRANDE', 100, 3),
                (30, 'PEQUENA', 100, 1), (30, 'MEDIA', 150, 1), (30, 'GRANDE', 200, 1),
                (31, 'PEQUENA', 100, 1), (31, 'MEDIA', 150, 1), (31, 'GRANDE', 200, 1),
                (32, 'PEQUENA', 100, 1), (32, 'MEDIA', 150, 1), (32, 'GRANDE', 200, 1),
                (33, 'PEQUENA', 2, 4), (33, 'MEDIA', 4, 4), (33, 'GRANDE', 6, 4),
                (34, 'PEQUENA', 50, 3), (34, 'MEDIA', 75, 3), (34, 'GRANDE', 100, 3),
                (35, 'PEQUENA', 100, 1), (35, 'MEDIA', 150, 1), (35, 'GRANDE', 200, 1),
                (36, 'PEQUENA', 100, 1), (36, 'MEDIA', 150, 1), (36, 'GRANDE', 200, 1),
                (37, 'PEQUENA', 100, 1), (37, 'MEDIA', 150, 1), (37, 'GRANDE', 200, 1),
                (38, 'PEQUENA', 100, 1), (38, 'MEDIA', 150, 1), (38, 'GRANDE', 200, 1),
                (39, 'PEQUENA', 100, 1), (39, 'MEDIA', 150, 1), (39, 'GRANDE', 200, 1),
                (40, 'PEQUENA', 100, 1), (40, 'MEDIA', 150, 1), (40, 'GRANDE', 200, 1),
                (41, 'PEQUENA', 50, 3), (41, 'MEDIA', 75, 3), (41, 'GRANDE', 100, 3),
                (42, 'PEQUENA', 100, 1), (42, 'MEDIA', 150, 1), (42, 'GRANDE', 200, 1),
                (43, 'PEQUENA', 100, 1), (43, 'MEDIA', 150, 1), (43, 'GRANDE', 200, 1),
                (44, 'PEQUENA', 100, 1), (44, 'MEDIA', 150, 1), (44, 'GRANDE', 200, 1);
        SQL;
        $this->execute($sql);
    }
}
