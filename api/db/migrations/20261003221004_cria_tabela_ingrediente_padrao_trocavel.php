<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredientePadraoTrocavel extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_padrao_trocavel (
                id INT PRIMARY KEY AUTO_INCREMENT,
                ingredientePadraoId INT NOT NULL,
                ingredienteId INT NOT NULL,
                UNIQUE KEY uq_trocavel_padrao_ingrediente (ingredientePadraoId, ingredienteId),
                CONSTRAINT fk_trocavel_ingrediente_padrao
                    FOREIGN KEY (ingredientePadraoId) REFERENCES ingrediente_padrao(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_trocavel_ingrediente
                    FOREIGN KEY (ingredienteId) REFERENCES ingrediente(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_padrao_trocavel');
    }
}
