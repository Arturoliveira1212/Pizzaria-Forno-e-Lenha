<?php

class RepositorioProdutoSimplesEmBDR implements RepositorioProdutoSimples {
    public const MAXIMO_TENTATIVAS_ATUALIZACAO_ESTOQUE = 3;

    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterProdutosSimples(FiltroProdutoSimples $filtro): array {
        try {
            $parametros = [];
            $resultado = $this->bancoDados->consultar($this->gerarQuery($filtro, $parametros), $parametros);
            if (empty($resultado)) {
                return [];
            }

            $produtosSimples = [];
            foreach ($resultado as $linha) {
                $produtosSimples[] = $this->criarProdutoSimples($linha);
            }

            return $produtosSimples;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter os produtos.');
        }
    }

    /**
     * @param int $idProduto
     * @return ProdutoSimples|null
     */
    public function obterProdutoSimplesPorId(int $idProduto): ?ProdutoSimples {
        try {
            $sql = 'SELECT
                produto_simples.id,
                produto_simples.nome,
                produto_simples.descricao,
                produto_simples.tipo,
                produto_simples.preco,
                produto_simples.estoque,
                produto_simples.idUnidadeMedida AS idUnidadeMedida,
                unidade_medida.descricao,
                unidade_medida.sigla
                    FROM produto_simples
                        INNER JOIN unidade_medida ON unidade_medida.id = produto_simples.idUnidadeMedida
                            WHERE produto_simples.id = :id';

            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $idProduto,
            ]);
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];

            return $this->criarProdutoSimples($linha);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter produto por ID.');
        }
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarProdutoSimples(array $linha): ProdutoSimples {
        return new ProdutoSimples(
            id: (int) $linha['id'],
            nome: $linha['nome'],
            descricao: $linha['descricao'],
            tipo: TipoProdutoSimples::from($linha['tipo']),
            preco: new Dinheiro((int) $linha['preco']),
            estoque: new QuantidadeMedida(
                valor: (int) $linha['estoque'],
                unidadeMedida: new UnidadeMedida(
                    id: (int) $linha['idUnidadeMedida'],
                    descricao: $linha['descricao'],
                    sigla: $linha['sigla'],
                ),
            ),
        );
    }

    /**
     * @param array<string, mixed> $parametros
     */
    private function gerarQuery(FiltroProdutoSimples $filtro, array &$parametros): string {
        $query = 'SELECT
            produto_simples.id,
            produto_simples.nome,
            produto_simples.descricao,
            produto_simples.tipo,
            produto_simples.preco,
            produto_simples.estoque,
            produto_simples.idUnidadeMedida AS idUnidadeMedida,
            unidade_medida.descricao,
            unidade_medida.sigla
                FROM produto_simples
                    INNER JOIN unidade_medida ON unidade_medida.id = produto_simples.idUnidadeMedida
                        WHERE 1=1';

        if ($filtro->identificador !== '') {
            $query .= ' AND (produto_simples.nome LIKE :nome OR produto_simples.id = :id)';

            $parametros['nome'] = "%{$filtro->identificador}%";
            $parametros['id'] = $filtro->identificador;
        }

        if ($filtro->tipo !== null) {
            $query .= ' AND produto_simples.tipo = :tipo';
            $parametros['tipo'] = $filtro->tipo->value;
        }

        if ($filtro->paginacao !== null) {
            $limite = $filtro->paginacao->limite;
            $offset = ($filtro->paginacao->pagina - 1) * $limite;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return $query;
    }

    public function atualizarEstoqueProdutoSimples(int $idProduto, QuantidadeMedida $quantidade, TipoOperacaoEstoque $tipoOperacaoEstoque): void {
        try {
            $versao = $this->obterVersaoProdutoSimples($idProduto);
            $sinal = $tipoOperacaoEstoque->sinal();

            $linhasAfetadas = 0;
            $tentativas = 0;

            do {
                $tentativas++;

                $sql = "UPDATE produto_simples SET
                    estoque = estoque {$sinal} :quantidade,
                    versao = :novaVersao
                        WHERE id = :id
                        AND versao = :versao";

                $linhasAfetadas = $this->bancoDados->executar($sql, [
                    'quantidade' => $quantidade->valor,
                    'id' => $idProduto,
                    'versao' => $versao,
                    'novaVersao' => $versao + 1,
                ]);

                $versao++;
            } while ($linhasAfetadas < 1 && $tentativas <= self::MAXIMO_TENTATIVAS_ATUALIZACAO_ESTOQUE);

            if ($linhasAfetadas === 0) {
                throw new RepositorioException('Não foi possível atualizar o estoque do produto. O produto pode ter sido atualizado por outro processo. Tente novamente.');
            }
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao atualizar o estoque do produto.');
        }
    }

    private function obterVersaoProdutoSimples(int $idProduto): int {
        try {
            $sql = 'SELECT versao FROM produto_simples WHERE id = :id';
            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $idProduto,
            ]);

            if (empty($resultado)) {
                throw new RepositorioException('Produto não encontrado.');
            }

            return (int) $resultado[0]['versao'];
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter a versão do produto.');
        }
    }
}
