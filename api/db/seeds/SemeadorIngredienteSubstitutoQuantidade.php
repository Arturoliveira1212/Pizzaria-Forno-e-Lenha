<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngredienteSubstitutoQuantidade extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorIngredienteSubstituto',
            'SemeadorUnidadeMedida',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente_substituto_quantidade
                (idIngredienteSubstituto, tamanho, quantidade, idUnidadeMedida) VALUES
                (1, 'PEQUENA', 100, 1), (1, 'MEDIA', 150, 1), (1, 'GRANDE', 200, 1),
                (2, 'PEQUENA', 100, 1), (2, 'MEDIA', 150, 1), (2, 'GRANDE', 200, 1),
                (3, 'PEQUENA', 100, 1), (3, 'MEDIA', 150, 1), (3, 'GRANDE', 200, 1),
                (4, 'PEQUENA', 100, 1), (4, 'MEDIA', 150, 1), (4, 'GRANDE', 200, 1),
                (5, 'PEQUENA', 100, 1), (5, 'MEDIA', 150, 1), (5, 'GRANDE', 200, 1),
                (6, 'PEQUENA', 100, 1), (6, 'MEDIA', 150, 1), (6, 'GRANDE', 200, 1),
                (7, 'PEQUENA', 100, 1), (7, 'MEDIA', 150, 1), (7, 'GRANDE', 200, 1),
                (8, 'PEQUENA', 100, 1), (8, 'MEDIA', 150, 1), (8, 'GRANDE', 200, 1),
                (9, 'PEQUENA', 100, 1), (9, 'MEDIA', 150, 1), (9, 'GRANDE', 200, 1),
                (10, 'PEQUENA', 100, 1), (10, 'MEDIA', 150, 1), (10, 'GRANDE', 200, 1),
                (11, 'PEQUENA', 100, 1), (11, 'MEDIA', 150, 1), (11, 'GRANDE', 200, 1),
                (12, 'PEQUENA', 100, 1), (12, 'MEDIA', 150, 1), (12, 'GRANDE', 200, 1),
                (13, 'PEQUENA', 100, 1), (13, 'MEDIA', 150, 1), (13, 'GRANDE', 200, 1),
                (14, 'PEQUENA', 100, 1), (14, 'MEDIA', 150, 1), (14, 'GRANDE', 200, 1),
                (15, 'PEQUENA', 100, 1), (15, 'MEDIA', 150, 1), (15, 'GRANDE', 200, 1),
                (16, 'PEQUENA', 100, 1), (16, 'MEDIA', 150, 1), (16, 'GRANDE', 200, 1),
                (17, 'PEQUENA', 100, 1), (17, 'MEDIA', 150, 1), (17, 'GRANDE', 200, 1),
                (18, 'PEQUENA', 100, 1), (18, 'MEDIA', 150, 1), (18, 'GRANDE', 200, 1),
                (19, 'PEQUENA', 100, 1), (19, 'MEDIA', 150, 1), (19, 'GRANDE', 200, 1),
                (20, 'PEQUENA', 100, 1), (20, 'MEDIA', 150, 1), (20, 'GRANDE', 200, 1);
        SQL;
        $this->execute($sql);
    }
}
