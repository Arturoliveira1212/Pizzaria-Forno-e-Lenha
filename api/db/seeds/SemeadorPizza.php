<?php

use Phinx\Seed\AbstractSeed;

class SemeadorPizza extends AbstractSeed {
    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO pizza (nome, descricao) VALUES
                ('Margherita', 'Molho de tomate, mussarela e manjericão fresco'),
                ('Calabresa', 'Molho de tomate, mussarela, calabresa fatiada e cebola'),
                ('Mussarela', 'Molho de tomate, mussarela e orégano'),
                ('Frango com Catupiry', 'Molho de tomate, frango desfiado, catupiry e milho'),
                ('Portuguesa', 'Molho de tomate, mussarela, presunto, ovo, cebola e azeitona'),
                ('Quatro Queijos', 'Molho de tomate, mussarela, provolone, parmesão e gorgonzola'),
                ('Pepperoni', 'Molho de tomate, mussarela e pepperoni fatiado'),
                ('Napolitana', 'Molho de tomate, mussarela, tomate fatiado, parmesão e manjericão'),
                ('Vegetariana', 'Molho de tomate, mussarela, pimentão, cebola, milho, azeitona e tomate'),
                ('Bacon', 'Molho de tomate, mussarela, bacon crocante e cebola');
        SQL;
        $this->execute($sql);
    }
}
