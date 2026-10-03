<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaIngredienteEscolhido extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE ingrediente_escolhido (
                id INT PRIMARY KEY AUTO_INCREMENT,
                saborEscolhidoId INT NOT NULL,
                ingredienteId INT NOT NULL,
                custo INT NOT NULL,
                papel ENUM('FIXO', 'ADICIONAL') NOT NULL,
                CONSTRAINT fk_ingrediente_escolhido_sabor
                    FOREIGN KEY (saborEscolhidoId) REFERENCES sabor_escolhido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_ingrediente_escolhido_ingrediente
                    FOREIGN KEY (ingredienteId) REFERENCES ingrediente(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE ingrediente_escolhido');
    }
}
