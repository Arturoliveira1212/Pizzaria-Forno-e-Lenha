<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredientePadraoQuantidade extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_padrao_quantidade (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idIngredientePadrao INT NOT NULL,
                tamanho ENUM('PEQUENA', 'MEDIA', 'GRANDE') NOT NULL,
                quantidade INT NOT NULL,
                idUnidadeMedida INT NOT NULL,
                UNIQUE KEY uq_ingrediente_padrao_quantidade_tamanho (idIngredientePadrao, tamanho),
                CONSTRAINT fk_ingrediente_padrao_quantidade_ingrediente_padrao
                    FOREIGN KEY (idIngredientePadrao) REFERENCES ingrediente_padrao(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ingrediente_padrao_quantidade_unidade_medida
                    FOREIGN KEY (idUnidadeMedida) REFERENCES unidade_medida(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_padrao_quantidade');
    }
}
