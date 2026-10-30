<?php

class SessaoPHP implements Sessao {
    public function __construct(
        private readonly string $nome = 'sid',
        private readonly int $duracaoEmSegundos = 30 * 60,
        private readonly bool $seguro = false,
    ) {}

    public function iniciar(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.gc_maxlifetime', (string) $this->duracaoEmSegundos);

        session_name($this->nome);
        session_set_cookie_params(['lifetime' => $this->duracaoEmSegundos] + $this->obterParametrosCookie());
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
        $_SESSION = [];
        setcookie(session_name(), '', ['expires' => time() - 3600] + $this->obterParametrosCookie());
        session_destroy();
    }

    /**
     * @return array{path: string, domain: string, secure: bool, httponly: bool, samesite: string}
     */
    private function obterParametrosCookie(): array {
        return [
            'path' => '/',
            'domain' => '',
            'secure' => $this->seguro,
            'httponly' => true,
            'samesite' => 'Strict',
        ];
    }
}
