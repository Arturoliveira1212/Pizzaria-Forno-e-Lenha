<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredienteEscolhido extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_escolhido (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idSaborEscolhido INT NOT NULL,
                idIngrediente INT NOT NULL,
                custo INT NOT NULL,
                papel ENUM('PADRAO', 'SUBSTITUTO', 'ADICIONAL') NOT NULL,
                CONSTRAINT fk_ingrediente_escolhido_sabor
                    FOREIGN KEY (idSaborEscolhido) REFERENCES sabor_escolhido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ingrediente_escolhido_ingrediente
                    FOREIGN KEY (idIngrediente) REFERENCES ingrediente(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_escolhido');
    }
}
