<?php

class DominioException extends RuntimeException {
    /**
     * @return string[]
     */
    public function erros(): array {
        return explode('|', $this->getMessage());
    }

    public static function criarException(string ...$mensagens): self {
        return new self(implode('|', $mensagens));
    }
}
