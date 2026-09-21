<?php

class BancoDados {
    public function __construct(
        public readonly PDO $pdo,
    ) {}

    /**
     * @param array<string, scalar|null> $parametros
     */
    public function rodar(string $comando, array $parametros = []): PDOStatement {
        $ps = $this->pdo->prepare($comando);
        $ps->execute($parametros);

        return $ps;
    }

    /**
     * @param array<string, scalar|null> $parametros
     */
    public function executar(string $comando, array $parametros = []): int {
        $ps = $this->rodar($comando, $parametros);

        return $ps->rowCount();
    }

    /**
     * @param array<string, scalar|null> $parametros
     * @return array<array<string, mixed>>
     */
    public function consultar(string $comando, array $parametros = []): array {
        $ps = $this->rodar($comando, $parametros);

        return $ps->fetchAll(PDO::FETCH_ASSOC);
    }

    public function executarComTransacao(callable $operacao): mixed {
        $transacaoAtiva = $this->emTransacao();
        $resultado = null;

        try {
            if (!$transacaoAtiva) {
                $this->iniciarTransacao();
            }

            $resultado = $operacao();

            if (!$transacaoAtiva) {
                $this->finalizarTransacao();
            }
        } catch (Throwable $e) {
            if (!$transacaoAtiva) {
                $this->desfazerTransacao();
            }

            throw $e;
        }

        return $resultado;
    }

    public function ultimoIdInserido(): string {
        return $this->pdo->lastInsertId();
    }

    public function iniciarTransacao(): void {
        $this->pdo->beginTransaction();
    }

    public function finalizarTransacao(): void {
        $this->pdo->commit();
    }

    public function desfazerTransacao(): void {
        $this->pdo->rollBack();
    }

    public function emTransacao(): bool {
        return $this->pdo->inTransaction();
    }
}
