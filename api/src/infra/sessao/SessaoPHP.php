<?php

class SessaoPHP implements Sessao {
    public function __construct(
        private readonly string $nome = 'sid',
        private readonly int $duracaoEmSegundos = 60 * 60 * 24,
        private readonly bool $seguro = false,
    ) {}

    public function iniciar(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name($this->nome);
        session_set_cookie_params($this->obterParametrosCookie());
        session_start();
    }

    public function obter(string $chave): mixed {
        return $_SESSION[$chave] ?? null;
    }

    public function definir(string $chave, mixed $valor): void {
        $_SESSION[$chave] = $valor;
    }

    public function limpar(): void {
        $_SESSION = [];
    }

    public function regenerar(): void {
        session_regenerate_id(true);
    }

    public function destruir(): void {
        session_destroy();
    }

    /**
     * @return array{lifetime: int, path: string, domain: string, secure: bool, httponly: bool, samesite: 'Lax'|'None'|'Strict'}
     */
    private function obterParametrosCookie(): array {
        return [
            'lifetime' => $this->duracaoEmSegundos,
            'path' => '/',
            'domain' => '',
            'secure' => $this->seguro,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }
}
