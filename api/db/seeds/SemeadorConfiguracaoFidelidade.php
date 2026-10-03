<?php

use Phinx\Seed\AbstractSeed;

class SemeadorConfiguracaoFidelidade extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO configuracao_fidelidade (valorMinimo, percentualDesconto, validadeEmDias) VALUES
                (5000, 10, 30);
        SQL;
        $this->execute($sql);
    }
}
