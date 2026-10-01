<?php

class Endereco {
    public function __construct(
        public readonly int $id,
        public readonly string $logradouro,
        public readonly string $numero,
        public readonly string $bairro,
        public readonly string $cidade,
        public readonly string $estado,
        public readonly string $uf,
    ) {}
}
