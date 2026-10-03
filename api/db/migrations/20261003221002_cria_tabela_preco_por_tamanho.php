<?php

use Phinx\Migration\AbstractMigration;

final class CriaTabelaPrecoPorTamanho extends AbstractMigration {
    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE preco_por_tamanho (
                id INT PRIMARY KEY AUTO_INCREMENT,
                pizzaId INT NOT NULL,
                tamanho ENUM('PEQUENA', 'MEDIA', 'GRANDE') NOT NULL,
                preco INT NOT NULL,
                UNIQUE KEY uq_preco_pizza_tamanho (pizzaId, tamanho),
                CONSTRAINT fk_preco_por_tamanho_pizza
                    FOREIGN KEY (pizzaId) REFERENCES pizza(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void {
        $this->execute('DROP TABLE preco_por_tamanho');
    }
}
