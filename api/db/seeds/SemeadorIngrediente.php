<?php

use Phinx\Seed\AbstractSeed;

class SemeadorIngrediente extends AbstractSeed {
    public function getDependencies(): array {
        return ['SemeadorUnidadeMedida'];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO ingrediente (nome, estoque, idUnidadeMedida) VALUES
                ('Molho de Tomate', 2500, 3),
                ('Mussarela', 9800, 1),
                ('Manjericão', 55, 4),
                ('Calabresa', 2500, 1),
                ('Cebola', 10080, 1),
                ('Frango Desfiado', 5000, 1),
                ('Catupiry', 8000, 1),
                ('Milho', 500, 1),
                ('Presunto', 12000, 1),
                ('Ovo', 160, 2),
                ('Azeitona', 1000, 1),
                ('Provolone', 250, 1),
                ('Parmesão', 5000, 1),
                ('Gorgonzola', 0, 1),
                ('Pepperoni', 100, 1),
                ('Tomate', 20000, 1),
                ('Pimentão', 8000, 1),
                ('Bacon', 3000, 1),
                ('Orégano', 980, 1);
        SQL;
        $this->execute($sql);
    }
}
