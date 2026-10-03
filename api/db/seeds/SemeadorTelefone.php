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
            INSERT INTO telefone (clienteId, ddd, numero) VALUES
                (1, '31', '991234567'),
                (2, '11', '998765432'),
                (3, '81', '996543210');
        SQL;
        $this->execute($sql);
    }
}
