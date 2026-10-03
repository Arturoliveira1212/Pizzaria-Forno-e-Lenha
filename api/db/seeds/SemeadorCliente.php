<?php

use Phinx\Seed\AbstractSeed;

class SemeadorCliente extends AbstractSeed {
    public function getDependencies(): array {
        return [
            'SemeadorEndereco',
        ];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO cliente (nome, enderecoId) VALUES
                ('Ana Beatriz Souza', 1),
                ('Carlos Eduardo Lima', 2),
                ('Fernanda Oliveira Costa', 3);
        SQL;
        $this->execute($sql);
    }
}
