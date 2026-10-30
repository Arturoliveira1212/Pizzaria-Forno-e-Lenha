<?php

class RepositorioUsuarioEmBDR implements RepositorioUsuario {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterUsuarioPorId(int $idUsuario): ?Usuario {
        try {
            $sql = 'SELECT
                id,
                nome,
                cpf,
                senha,
                papel
                    FROM usuario
                    WHERE id = :id';

            $resultado = $this->bancoDados->consultar($sql, [
                'id' => $idUsuario,
            ]);
            if (empty($resultado)) {
                return null;
            }

            return $this->criarUsuario($resultado[0]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter usuário por ID.');
        }
    }

    public function obterUsuarioPorCpf(string $cpf): ?Usuario {
        try {
            $sql = 'SELECT
                id,
                nome,
                cpf,
                senha,
                papel
                    FROM usuario
                    WHERE cpf = :cpf';

            $resultado = $this->bancoDados->consultar($sql, [
                'cpf' => $cpf,
            ]);
            if (empty($resultado)) {
                return null;
            }

            return $this->criarUsuario($resultado[0]);
        } catch (Throwable $e) {
            throw new RepositorioException('Erro ao obter usuário por CPF.');
        }
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function criarUsuario(array $linha): Usuario {
        return new Usuario(
            id: (int) $linha['id'],
            nome: $linha['nome'],
            cpf: new Cpf($linha['cpf']),
            senha: $linha['senha'],
            papel: PapelUsuario::from($linha['papel']),
        );
    }
}
