<?php

class RepositorioEntregadorEmBDR implements RepositorioEntregador {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterEntregadoresDisponiveis(): array {
        try {
            $sql = 'SELECT
                entregador.id,
                entregador.nome,
                entregador.ddd,
                entregador.numero,
                entregador.disponivel
                    FROM entregador
                        WHERE entregador.disponivel = :disponivel
                        ORDER BY entregador.nome';

            $resultado = $this->bancoDados->consultar($sql, [
                'disponivel' => 1,
            ]);
            if (empty($resultado)) {
                return [];
            }

            $entregadores = [];
            foreach ($resultado as $linha) {
                $entregadores[] = $this->criarEntregador($linha);
            }

            return $entregadores;
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter entregadores disponíveis.');
        }
    }

    public function obterEntregadorPorId(int $idEntregador): ?Entregador {
        try {
            $sql = 'SELECT
                entregador.id,
                entregador.nome,
                entregador.ddd,
                entregador.numero,
                entregador.disponivel
                    FROM entregador
                        WHERE entregador.id = :id';

            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $idEntregador,
            ]);
            if (empty($resultado)) {
                return null;
            }

            $linha = $resultado[0];

            return $this->criarEntregador($linha);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter entregador por ID.');
        }
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarEntregador(array $linha): Entregador {
        return new Entregador(
            id: (int) $linha['id'],
            nome: $linha['nome'],
            telefone: Telefone::criar($linha['ddd'] . $linha['numero']),
            disponivel: (bool) $linha['disponivel'],
        );
    }
}
