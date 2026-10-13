<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredienteAdicional extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_adicional (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idIngrediente INT NOT NULL,
                preco INT NOT NULL,
                CONSTRAINT fk_ingrediente_adicional_ingrediente
                    FOREIGN KEY (idIngrediente) REFERENCES ingrediente(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_adicional');
    }
}
