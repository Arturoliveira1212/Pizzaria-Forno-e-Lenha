<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaMovimentacaoEstoqueIngrediente extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE movimentacao_estoque_ingrediente (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idIngrediente INT NOT NULL,
                data DATETIME NOT NULL,
                tipo ENUM('ENTRADA', 'SAIDA') NOT NULL,
                quantidade INT NOT NULL,
                idUnidadeMedida INT NOT NULL,
                CONSTRAINT fk_movimentacao_estoque_ingrediente
                    FOREIGN KEY (idIngrediente) REFERENCES ingrediente(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_movimentacao_estoque_ingrediente_unidade_medida
                    FOREIGN KEY (idUnidadeMedida) REFERENCES unidade_medida(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE movimentacao_estoque_ingrediente');
    }
}
