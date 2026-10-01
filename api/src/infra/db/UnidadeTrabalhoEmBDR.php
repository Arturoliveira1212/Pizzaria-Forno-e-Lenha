<?php

class UnidadeTrabalhoEmBDR implements UnidadeTrabalho {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function executar(callable $operacao): void {
        $this->bancoDados->executarComTransacao($operacao);
    }
}
