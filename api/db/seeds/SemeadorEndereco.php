<?php

use Phinx\Seed\AbstractSeed;

class SemeadorEndereco extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorCliente',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO endereco (idCliente, logradouro, numero, bairro, cidade, estado, uf) VALUES
                (1, 'Rua das Acácias', '123', 'Centro', 'Belo Horizonte', 'Minas Gerais', 'MG'),
                (2, 'Avenida Paulista', '1578', 'Bela Vista', 'São Paulo', 'São Paulo', 'SP'),
                (3, 'Rua do Sol', '45', 'Boa Viagem', 'Recife', 'Pernambuco', 'PE');
        SQL;
        $this->execute($sql);
    }
}
