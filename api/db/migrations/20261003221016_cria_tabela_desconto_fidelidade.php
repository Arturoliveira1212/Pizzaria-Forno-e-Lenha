<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaDescontoFidelidade extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE desconto_fidelidade (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pedidoId INT NOT NULL,
                valorDesconto INT NOT NULL,
                beneficioFidelidadeId INT NOT NULL,
                CONSTRAINT fk_desconto_fidelidade_pedido
                    FOREIGN KEY (pedidoId) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_desconto_fidelidade_beneficio
                    FOREIGN KEY (beneficioFidelidadeId) REFERENCES beneficio_fidelidade(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE desconto_fidelidade');
    }
}
