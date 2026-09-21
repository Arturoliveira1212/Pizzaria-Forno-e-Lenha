<?php

class Cancelamento {
    public function __construct(
        public readonly int $id,
        public readonly string $motivo,
        public readonly DateTime $data,
    ) {}
}
