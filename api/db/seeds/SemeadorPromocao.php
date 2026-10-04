<?php

use Phinx\Seed\AbstractSeed;

class SemeadorPromocao extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO promocao (descricao, percentualDesconto, quantidadeMinima, dataInicio, validade, tamanho) VALUES
                ('Promoção Dia das Crianças', 10, 2, '2026-10-01', '2026-10-12', 'GRANDE');
        SQL;
        $this->execute($sql);
    }
}
