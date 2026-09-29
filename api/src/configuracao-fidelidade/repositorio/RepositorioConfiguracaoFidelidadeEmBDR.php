<?php

class RepositorioConfiguracaoFidelidadeEmBDR implements RepositorioConfiguracaoFidelidade {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterConfiguracaoFidelidade(): ?ConfiguracaoFidelidade {
        return null;
    }
}
