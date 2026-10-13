<?php

use Phinx\Seed\AbstractSeed;

class SemeadorPromocao extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO promocao (descricao, percentualDesconto, quantidadeMinima, dataInicio, validade, tamanho) VALUES
                ('Promoção', 10, 2, '2026-01-01', NULL, 'GRANDE');
        SQL;
        $this->execute($sql);
    }
}
