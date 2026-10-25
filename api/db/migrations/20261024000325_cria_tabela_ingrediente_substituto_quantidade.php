<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredienteSubstitutoQuantidade extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_substituto_quantidade (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idIngredienteSubstituto INT NOT NULL,
                tamanho ENUM('PEQUENA', 'MEDIA', 'GRANDE') NOT NULL,
                quantidade INT UNSIGNED NOT NULL,
                idUnidadeMedida INT NOT NULL,
                UNIQUE KEY uq_ingrediente_substituto_quantidade_tamanho (idIngredienteSubstituto, tamanho),
                CONSTRAINT fk_ingrediente_substituto_quantidade_ingrediente_substituto
                    FOREIGN KEY (idIngredienteSubstituto) REFERENCES ingrediente_substituto(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ingrediente_substituto_quantidade_unidade_medida
                    FOREIGN KEY (idUnidadeMedida) REFERENCES unidade_medida(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_substituto_quantidade');
    }
}
