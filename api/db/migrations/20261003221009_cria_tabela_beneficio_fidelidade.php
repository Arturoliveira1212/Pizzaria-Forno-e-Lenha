<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaBeneficioFidelidade extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE beneficio_fidelidade (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idCliente INT NOT NULL,
                percentualDesconto INT NOT NULL,
                dataConcessao DATE NOT NULL,
                validade DATE NOT NULL,
                utilizado BOOLEAN NOT NULL DEFAULT FALSE,
                CONSTRAINT fk_beneficio_fidelidade_cliente
                    FOREIGN KEY (idCliente) REFERENCES cliente(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE beneficio_fidelidade');
    }
}
