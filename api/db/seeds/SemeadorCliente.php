<?php

use Phinx\Seed\AbstractSeed;

class SemeadorCliente extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO cliente (nome, cpf) VALUES
                ('Ana Beatriz Souza', '12345678901'),
                ('Carlos Eduardo Lima', '23456789012'),
                ('Fernanda Oliveira Costa', '34567890123');
        SQL;
        $this->execute($sql);
    }
}
