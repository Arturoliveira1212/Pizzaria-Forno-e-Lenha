<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngrediente extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente (nome) VALUES
                ('Molho de Tomate'),
                ('Mussarela'),
                ('Manjericão'),
                ('Calabresa'),
                ('Cebola'),
                ('Frango Desfiado'),
                ('Catupiry'),
                ('Milho'),
                ('Presunto'),
                ('Ovo'),
                ('Azeitona'),
                ('Provolone'),
                ('Parmesão'),
                ('Gorgonzola'),
                ('Pepperoni'),
                ('Tomate'),
                ('Pimentão'),
                ('Bacon'),
                ('Orégano');
        SQL;
        $this->execute($sql);
    }
}
