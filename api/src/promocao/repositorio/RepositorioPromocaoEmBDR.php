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
                    dataInicio: $linha['dataInicio'] ? new DateTime($linha['dataInicio']) : null,
                    dataFim: $linha['validade'] ? new DateTime($linha['validade']) : null,
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
            $query .= ' AND (dataInicio IS NULL OR dataInicio <= CURDATE())
                AND (validade IS NULL OR validade   >= CURDATE());';
        }

        if ($filtro->paginacao !== null) {
            $limite = $filtro->paginacao->limite;
            $offset = ($filtro->paginacao->pagina - 1) * $limite;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return $query;
    }

    public function obterPromocaoPorId(int $id): ?Promocao {
        try {
            $sql = 'SELECT
                promocao.id,
                promocao.descricao,
                promocao.quantidadeMinima,
                promocao.tamanho,
                promocao.percentualDesconto,
                promocao.dataInicio,
                promocao.validade
                    FROM promocao
                        WHERE promocao.id = :id';

            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $id,
            ]);
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];

            return new Promocao(
                id: (int) $linha['id'],
                descricao: $linha['descricao'],
                quantidadeMinima: (int) $linha['quantidadeMinima'],
                tamanhoPizza: TamanhoPizza::from($linha['tamanho']),
                percentualDesconto: new Percentual((int) $linha['percentualDesconto']),
                dataInicio: $linha['dataInicio'] ? new DateTime($linha['dataInicio']) : null,
                dataFim: $linha['validade'] ? new DateTime($linha['validade']) : null,
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter promoção por ID.');
        }
    }
}
