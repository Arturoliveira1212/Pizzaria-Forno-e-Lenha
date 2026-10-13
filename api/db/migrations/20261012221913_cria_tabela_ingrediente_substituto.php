<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredienteSubstituto extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_substituto (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idIngrediente INT NOT NULL,
                idPizza INT NOT NULL,
                CONSTRAINT fk_ingrediente_substituto_ingrediente
                    FOREIGN KEY (idIngrediente) REFERENCES ingrediente(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ingrediente_substituto_pizza
                    FOREIGN KEY (idPizza) REFERENCES pizza(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_substituto');
    }
}
