<?php

use Phinx\Seed\AbstractSeed;

class SemeadorEndereco extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO endereco (logradouro, bairro, cidade, uf) VALUES
                ('Rua das Acácias, 123', 'Centro', 'Belo Horizonte', 'MG'),
                ('Avenida Paulista, 1578', 'Bela Vista', 'São Paulo', 'SP'),
                ('Rua do Sol, 45', 'Boa Viagem', 'Recife', 'PE');
        SQL;
        $this->execute($sql);
    }
}
