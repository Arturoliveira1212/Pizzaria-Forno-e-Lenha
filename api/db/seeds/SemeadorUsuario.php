<?php

use Phinx\Seed\AbstractSeed;

class SemeadorUsuario extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $geradorHashSenha = new GeradorHashSenha($_ENV['PIMENTA']);
        $senhaAdministrador = $geradorHashSenha->gerar('admin123');
        $senhaAtendente = $geradorHashSenha->gerar('atendente123');

        $sql = <<<SQL
            INSERT INTO usuario (nome, cpf, senha, papel) VALUES
                ('Miguel Coves', '12345678901', '{$senhaAdministrador}', 'ADMINISTRADOR'),
                ('Mariana Silva', '98765432100', '{$senhaAtendente}', 'ATENDENTE');
        SQL;
        $this->execute($sql);
    }
}
