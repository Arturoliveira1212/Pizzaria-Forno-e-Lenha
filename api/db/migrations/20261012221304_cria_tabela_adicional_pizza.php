<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaAdicionalPizza extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE adicional_pizza (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idIngredienteAdicional INT NOT NULL,
                idPizza INT NOT NULL,
                CONSTRAINT fk_adicional_pizza_ingrediente_adicional
                    FOREIGN KEY (idIngredienteAdicional) REFERENCES ingrediente_adicional(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_adicional_pizza_pizza
                    FOREIGN KEY (idPizza) REFERENCES pizza(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE adicional_pizza');
    }
}
