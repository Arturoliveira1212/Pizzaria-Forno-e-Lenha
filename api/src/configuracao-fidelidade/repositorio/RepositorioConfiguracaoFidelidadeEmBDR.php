<?php

class RepositorioConfiguracaoFidelidadeEmBDR implements RepositorioConfiguracaoFidelidade {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterConfiguracaoFidelidade(): ?ConfiguracaoFidelidade {
        try {
            $resultado = $this->bancoDados->consultar(
                'SELECT
                    configuracao_fidelidade.id,
                    configuracao_fidelidade.valorMinimo,
                    configuracao_fidelidade.percentualDesconto,
                    configuracao_fidelidade.validadeEmDias
                        FROM configuracao_fidelidade
                            LIMIT 1',
            );
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];

            return new ConfiguracaoFidelidade(
                id: (int) $linha['id'],
                valorMinimo: new Dinheiro((int) $linha['valorMinimo']),
                percentual: new Percentual((int) $linha['percentualDesconto']),
                validadeEmDias: (int) $linha['validadeEmDias'],
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter configuração de fidelidade.');
        }
    }
}
