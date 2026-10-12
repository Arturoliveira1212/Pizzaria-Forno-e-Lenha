<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaDescontoPromocao extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE desconto_promocao (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idPedido INT NOT NULL,
                valorDesconto INT NOT NULL,
                idPromocao INT NOT NULL,
                CONSTRAINT fk_desconto_promocao_pedido
                    FOREIGN KEY (idPedido) REFERENCES pedido(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_desconto_promocao_promocao
                    FOREIGN KEY (idPromocao) REFERENCES promocao(id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE desconto_promocao');
    }
}
