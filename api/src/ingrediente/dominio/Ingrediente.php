<?php

class Ingrediente {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly QuantidadeMedida $estoque,
    ) {}

    public function temEstoqueSuficiente(QuantidadeMedida $consumo): bool {
        return $consumo->ehMenorOuIgualA($this->estoque);
    }

    public function copiarComEstoque(QuantidadeMedida $novoEstoque): Ingrediente {
        return new Ingrediente(
            id: $this->id,
            nome: $this->nome,
            estoque: $novoEstoque,
        );
    }
}
