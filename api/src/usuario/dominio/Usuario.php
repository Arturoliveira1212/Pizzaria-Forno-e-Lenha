<?php

class Usuario implements JsonSerializable {
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly Cpf $cpf,
        public readonly string $senha,
        public readonly PapelUsuario $papel,
    ) {}

    /**
     * @return array{id: int, nome: string, papel: string}
     */
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'papel' => $this->papel->value,
        ];
    }
}
