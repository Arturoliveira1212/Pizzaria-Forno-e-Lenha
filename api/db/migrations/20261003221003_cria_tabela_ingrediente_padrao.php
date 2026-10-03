<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredientePadrao extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_padrao (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pizzaId INT NOT NULL,
                ingredienteId INT NOT NULL,
                removivel BOOLEAN NOT NULL DEFAULT TRUE,
                substituivel BOOLEAN NOT NULL DEFAULT TRUE,
                CONSTRAINT fk_ingrediente_padrao_pizza
                    FOREIGN KEY (pizzaId) REFERENCES pizza(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ingrediente_padrao_ingrediente
                    FOREIGN KEY (ingredienteId) REFERENCES ingrediente(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_padrao');
    }
}
