<?php

use Phinx\Seed\AbstractSeed;

class SemeadorUnidadeMedida extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO unidade_medida (descricao, sigla) VALUES
                ('Gramas', 'g'),
                ('Unidade', 'un'),
                ('Mililitros', 'ml'),
                ('Folhas', 'folhas');
        SQL;
        $this->execute($sql);
    }
}
