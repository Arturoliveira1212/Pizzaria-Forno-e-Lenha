<?php

class RepositorioPromocaoEmBDR implements RepositorioPromocao {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterPromocoes(FiltroPromocao $filtro): array {
        try {
            $parametros = [];
            $resultado = $this->bancoDados->consultar($this->gerarQuery($filtro, $parametros), $parametros);
            if (empty($resultado)) {
                return [];
            }

            $promocoes = [];
            foreach ($resultado as $linha) {
                $promocoes[] = new Promocao(
                    id: (int) $linha['id'],
                    descricao: $linha['descricao'],
                    quantidadeMinima: (int) $linha['quantidadeMinima'],
                    tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
                    percentualDesconto: new Percentual((int) $linha['percentualDesconto']),
                    dataInicio: new DateTime($linha['dataInicio']),
                    dataFim: new DateTime($linha['validade']),
                );
            }

            return $promocoes;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter promoções.');
        }
    }

    /**
     * @param array<string, mixed> $parametros
     */
    private function gerarQuery(FiltroPromocao $filtro, array &$parametros): string {
        $query = 'SELECT
            promocao.id,
            promocao.descricao,
            promocao.quantidadeMinima,
            promocao.tamanho,
            promocao.percentualDesconto,
            promocao.dataInicio,
            promocao.validade
                FROM promocao
                    WHERE 1=1';

        if ($filtro->apenasVigentes) {
            $query .= ' AND promocao.dataInicio <= CURDATE()
                AND promocao.validade >= CURDATE()';
        }

        if ($filtro->paginacao !== null) {
            $limite = $filtro->paginacao->limite;
            $offset = ($filtro->paginacao->pagina - 1) * $limite;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return $query;
    }
}
