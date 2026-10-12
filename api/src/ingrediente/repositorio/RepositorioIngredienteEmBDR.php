<?php

class RepositorioIngredienteEmBDR implements RepositorioIngrediente {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterIngredientePorId(int $idIngrediente): ?Ingrediente {
        try {
            $sql = 'SELECT ingrediente.id, ingrediente.nome
                FROM ingrediente
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
            );
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter ingrediente por ID.');
        }
    }
}
