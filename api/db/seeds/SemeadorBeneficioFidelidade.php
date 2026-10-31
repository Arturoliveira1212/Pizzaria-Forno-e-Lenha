<?php

use Phinx\Seed\AbstractSeed;

class SemeadorBeneficioFidelidade extends AbstractSeed {
    public function getDependencies(): array {
        return ['SemeadorCliente'];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO beneficio_fidelidade (idCliente, percentualDesconto, dataConcessao, validade, utilizado) VALUES
                (11, 10, '2026-01-01', '2026-12-31', 0),
                (10, 10, '2026-01-01', '2026-12-31', 0);
        SQL;
        $this->execute($sql);
    }
}
