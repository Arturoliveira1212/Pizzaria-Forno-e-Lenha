<?php

class Telefone {
    public function __construct(
        public readonly string $ddd,
        public readonly string $numero,
    ) {}
}
