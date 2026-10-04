<?php

class Cpf implements JsonSerializable {
    public function __construct(
        public readonly string $numero,
    ) {}

    public function formatar(): string {
        return substr($this->numero, 0, 3) . '.'
               . substr($this->numero, 3, 3) . '.'
               . substr($this->numero, 6, 3) . '-'
               . substr($this->numero, 9, 2);
    }

    public function jsonSerialize(): mixed {
        return $this->formatar();
    }
}
