<?php

class PeriodoRelatorio {
    public function __construct(
        public readonly DateTime $dataInicio,
        public readonly DateTime $dataFim,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        if ($this->dataInicio > $this->dataFim) {
            throw new DominioException('A data de início deve ser anterior ou igual à data de fim.');
        }
    }
}
