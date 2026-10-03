<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaEntrega extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE entrega (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pedidoId INT NOT NULL,
                dataSaida DATETIME NULL,
                dataChegada DATETIME NULL,
                taxa INT NOT NULL,
                enderecoId INT NOT NULL,
                entregadorId INT NOT NULL,
                UNIQUE KEY uq_entrega_pedido (pedidoId),
                CONSTRAINT fk_entrega_pedido
                    FOREIGN KEY (pedidoId) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_entrega_endereco
                    FOREIGN KEY (enderecoId) REFERENCES endereco(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_entrega_entregador
                    FOREIGN KEY (entregadorId) REFERENCES entregador(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE entrega');
    }
}
