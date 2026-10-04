<?php

use Phinx\Seed\AbstractSeed;

class SemeadorTelefone extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorCliente',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO telefone (idCliente, ddd, numero) VALUES
                (1, '31', '991234567'),
                (2, '11', '998765432'),
                (3, '81', '996543210'),
                (4, '21', '995432109'),
                (5, '41', '994321098'),
                (6, '51', '993210987'),
                (7, '61', '992109876'),
                (8, '71', '991098765'),
                (9, '81', '990987654'),
                (10, '91', '989876543'),
                (11, '11', '988765432'),
                (12, '21', '987654321');
        SQL;
        $this->execute($sql);
    }
}
