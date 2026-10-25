<?php

class RepositorioIngredienteEmBDR implements RepositorioIngrediente {
    public const MAXIMO_TENTATIVAS_ATUALIZACAO_ESTOQUE = 3;

    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterIngredientePorId(int $idIngrediente): ?Ingrediente {
        try {
            $sql = 'SELECT
                ingrediente.id,
                ingrediente.nome,
                ingrediente.estoque,
                unidade_medida.id AS idUnidadeMedida,
                unidade_medida.descricao,
                unidade_medida.sigla
                    FROM ingrediente
                        INNER JOIN unidade_medida ON unidade_medida.id = ingrediente.idUnidadeMedida
                            WHERE ingrediente.id = :id';

            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $idIngrediente,
            ]);
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];

            return new Ingrediente(
                id: (int) $linha['id'],
                nome: $linha['nome'],
                estoque: new QuantidadeMedida(
                    valor: (int) $linha['estoque'],
                    unidadeMedida: new UnidadeMedida(
                        id: (int) $linha['idUnidadeMedida'],
                        descricao: $linha['descricao'],
                        sigla: $linha['sigla'],
                    ),
                ),
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter ingrediente por ID.');
        }
    }

    public function atualizarEstoqueIngrediente(int $idIngrediente, QuantidadeMedida $quantidade, TipoOperacaoEstoque $tipoOperacaoEstoque): void {
        try {
            $versao = $this->obterVersaoIngrediente($idIngrediente);
            $sinal = $tipoOperacaoEstoque->sinal();

            $linhasAfetadas = 0;
            $tentativas = 0;

            do {
                $tentativas++;

                $sql = "UPDATE ingrediente SET
                    estoque = estoque {$sinal} :quantidade,
                    versao = :novaVersao
                        WHERE id = :id
                        AND versao = :versao";

                $linhasAfetadas = $this->bancoDados->executar($sql, [
                    'quantidade' => $quantidade->valor,
                    'id' => $idIngrediente,
                    'versao' => $versao,
                    'novaVersao' => $versao + 1,
                ]);

                $versao++;
            } while ($linhasAfetadas < 1 && $tentativas <= self::MAXIMO_TENTATIVAS_ATUALIZACAO_ESTOQUE);

            if ($linhasAfetadas === 0) {
                throw new RepositorioException('Não foi possível atualizar o estoque do ingrediente. O ingrediente pode ter sido atualizado por outro processo. Tente novamente.');
            }
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao atualizar o estoque do ingrediente.');
        }
    }

    private function obterVersaoIngrediente(int $idIngrediente): int {
        $sql = 'SELECT versao FROM ingrediente WHERE id = :id';
        $resultado = $this->bancoDados->consultar($sql, [
            'id' => $idIngrediente,
        ]);
        if (empty($resultado)) {
            throw new RepositorioException('Ingrediente não encontrado.');
        }

        return (int) $resultado[0]['versao'];
    }
}
