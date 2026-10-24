<?php

use Phinx\Seed\AbstractSeed;

class SemeadorUsuario extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO usuario (nome, cpf, senha, papel) VALUES
                ('Miguel Coves', '12345678901', 'admin123', 'ADMINISTRADOR'),
                ('Mariana Silva', '98765432100', 'atendente123', 'ATENDENTE');
        SQL;
        $this->execute($sql);
    }
}
