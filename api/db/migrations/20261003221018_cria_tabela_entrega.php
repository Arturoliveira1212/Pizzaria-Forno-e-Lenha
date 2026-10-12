<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaEntrega extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE entrega (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idPedido INT NOT NULL,
                dataSaida DATETIME NULL,
                dataChegada DATETIME NULL,
                taxa INT NOT NULL,
                idEndereco INT NOT NULL,
                idEntregador INT NOT NULL,
                UNIQUE KEY uq_entrega_pedido (idPedido),
                CONSTRAINT fk_entrega_pedido
                    FOREIGN KEY (idPedido) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_entrega_endereco
                    FOREIGN KEY (idEndereco) REFERENCES endereco(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                CONSTRAINT fk_entrega_entregador
                    FOREIGN KEY (idEntregador) REFERENCES entregador(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE entrega');
    }
}
