<?php

class RepositorioIngredienteEmBDR implements RepositorioIngrediente {
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
                estoque: new Quantidade(
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
}
