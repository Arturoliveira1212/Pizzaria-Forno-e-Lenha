<?php

use Phinx\Seed\AbstractSeed;

class SemeadorCliente extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO cliente (nome, cpf) VALUES
                ('Ana Beatriz Souza', '52998224725'),
                ('Carlos Eduardo Lima', '11144477735'),
                ('Fernanda Oliveira Costa', '12345678909'),
                ('Gabriel Santos Rocha', '98765432100'),
                ('Guilherme Rosa', '32165498791'),
                ('Juliana Pereira Martins', '14725836982'),
                ('Lucas Almeida Ferreira', '25836914737'),
                ('Mariana Rodrigues Silva', '36925814755'),
                ('Rafael Costa Gomes', '74185296355'),
                ('Sofia Lima Barbosa', '85296374100'),
                ('Artur Alves', '15975348625'),
                ('Thiago Fernandes Nascimento', '24681357928');
        SQL;
        $this->execute($sql);
    }
}
