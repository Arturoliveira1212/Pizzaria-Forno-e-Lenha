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
                (1, 'Rua das Acácias', '123', 'Centro', 'Duas Barras', 'Rio de Janeiro', 'RJ'),
                (2, 'Avenida Alberto Braune', '1578', 'Centro', 'Nova Friburgo', 'Rio de Janeiro', 'RJ'),
                (3, 'Rua do Sol', '45', 'Olaria', 'Nova Friburgo', 'Rio de Janeiro', 'RJ'),
                (4, 'Rua das Flores', '789', 'Centro', 'Bom Jardim', 'Rio de Janeiro', 'RJ'),
                (5, 'Avenida Brasil', '2000', 'Centro', 'Cantagalo', 'Rio de Janeiro', 'RJ'),
                (6, 'Rua das Palmeiras', '321', 'Centro', 'Cachoeiras de Macacu', 'Rio de Janeiro', 'RJ'),
                (7, 'Rua das Laranjeiras', '1500', 'Japuíba', 'Cachoeiras de Macacu', 'Rio de Janeiro', 'RJ'),
                (8, 'Avenida Getúlio Vargas', '456', 'Centro', 'Cordeiro', 'Rio de Janeiro', 'RJ'),
                (9, 'Rua das Américas', '3000', 'Centro', 'Macuco', 'Rio de Janeiro', 'RJ'),
                (10, 'Rua das Margaridas', '7890', 'Cônego', 'Nova Friburgo', 'Rio de Janeiro', 'RJ'),
                (11, 'Rua dos Girassóis', '1234', 'Centro', 'Cantagalo', 'Rio de Janeiro', 'RJ'),
                (12, 'Avenida das Nações', '5678', 'Centro', 'Bom Jardim', 'Rio de Janeiro', 'RJ');
        SQL;
        $this->execute($sql);
    }
}
