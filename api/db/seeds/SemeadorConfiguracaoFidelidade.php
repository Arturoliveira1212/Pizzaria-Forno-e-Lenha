<?php

use Phinx\Seed\AbstractSeed;

class SemeadorConfiguracaoFidelidade extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO configuracao_fidelidade (valorMinimo, percentualDesconto, validadeEmDias) VALUES
                (10000, 5, 7);
        SQL;
        $this->execute($sql);
    }
}
