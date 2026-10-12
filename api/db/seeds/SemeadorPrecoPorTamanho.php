<?php

use Phinx\Seed\AbstractSeed;

class SemeadorPrecoPorTamanho extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorPizza',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO preco_por_tamanho (idPizza, tamanho, preco) VALUES
                (1, 'PEQUENA', 3200), (1, 'MEDIA', 4200), (1, 'GRANDE', 5200),
                (2, 'PEQUENA', 3300), (2, 'MEDIA', 4300), (2, 'GRANDE', 5300),
                (3, 'PEQUENA', 3000), (3, 'MEDIA', 4000), (3, 'GRANDE', 5000),
                (4, 'PEQUENA', 3800), (4, 'MEDIA', 4800), (4, 'GRANDE', 5800),
                (5, 'PEQUENA', 3700), (5, 'MEDIA', 4700), (5, 'GRANDE', 5700),
                (6, 'PEQUENA', 4000), (6, 'MEDIA', 5000), (6, 'GRANDE', 6000),
                (7, 'PEQUENA', 3900), (7, 'MEDIA', 4900), (7, 'GRANDE', 5900),
                (8, 'PEQUENA', 3600), (8, 'MEDIA', 4600), (8, 'GRANDE', 5600),
                (9, 'PEQUENA', 3400), (9, 'MEDIA', 4400), (9, 'GRANDE', 5400),
                (10, 'PEQUENA', 3800), (10, 'MEDIA', 4800), (10, 'GRANDE', 5800);
        SQL;
        $this->execute($sql);
    }
}
