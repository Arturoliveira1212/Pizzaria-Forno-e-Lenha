<?php

class Cancelamento {
    public function __construct(
        public readonly int $id,
        public readonly string $motivo,
        public readonly DateTime $data,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if (trim($this->motivo) === '') {
            $erros[] = 'O motivo do cancelamento é obrigatório.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }
}
