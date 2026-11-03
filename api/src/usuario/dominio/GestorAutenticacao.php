<?php

class GestorAutenticacao implements ProvedorUsuarioLogado {
    public const CHAVE_ID_USUARIO = 'idUsuario';
    public const CHAVE_PAPEL = 'papel';
    private const CHAVE_IP = 'ip';
    private const CHAVE_NAVEGADOR = 'navegador';
    private const HASH_FICTICIO = '$argon2id$v=19$m=65536,t=4,p=1$VGRSZkRFNmg5ZFlZLmcxZQ$QYewcI/2z/yU9kpuYuTMHNd7WauROm8nWzRltPaG2eo';

    public function __construct(
        private readonly RepositorioUsuario $repositorioUsuario,
        private readonly Sessao $sessao,
        private readonly GeradorHashSenha $geradorHashSenha,
    ) {}

    public function entrar(string $cpf, string $senha, string $ip, string $navegador): Usuario {
        $numeroCpf = preg_replace('/\D/', '', $cpf) ?? '';

        $erros = [];
        if (strlen($numeroCpf) !== 11) {
            $erros[] = 'O CPF deve conter 11 dígitos.';
        }

        if ($senha === '') {
            $erros[] = 'A senha é obrigatória.';
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }

        $usuario = $this->repositorioUsuario->obterUsuarioPorCpf($numeroCpf);
        $senhaCorreta = $this->geradorHashSenha->verificar($senha, $usuario->senha ?? self::HASH_FICTICIO);
        if (!$usuario instanceof Usuario || !$senhaCorreta) {
            throw new AutenticacaoException('CPF ou senha incorretos.');
        }

        $this->sessao->iniciar();
        $this->sessao->regenerar();
        $this->sessao->limpar();
        $this->sessao->definir(self::CHAVE_ID_USUARIO, $usuario->id);
        $this->sessao->definir(self::CHAVE_PAPEL, $usuario->papel->value);
        $this->sessao->definir(self::CHAVE_IP, $ip);
        $this->sessao->definir(self::CHAVE_NAVEGADOR, $navegador);

        return $usuario;
    }

    public function sair(): void {
        $this->sessao->iniciar();
        $this->sessao->destruir();
    }

    public function validarSessao(string $ip, string $navegador): void {
        $this->sessao->iniciar();

        if (!is_int($this->sessao->obter(self::CHAVE_ID_USUARIO))) {
            throw new AutenticacaoException('É necessário entrar no sistema.');
        }

        if ($ip !== $this->sessao->obter(self::CHAVE_IP) || $navegador !== $this->sessao->obter(self::CHAVE_NAVEGADOR)) {
            $this->sessao->destruir();
            throw new AutenticacaoException('Sessão inválida. Entre novamente no sistema.');
        }
    }

    public function obterUsuarioLogado(): Usuario {
        $this->sessao->iniciar();

        $idUsuario = $this->sessao->obter(self::CHAVE_ID_USUARIO);
        if (!is_int($idUsuario)) {
            throw new AutenticacaoException('É necessário entrar no sistema.');
        }

        $usuario = $this->repositorioUsuario->obterUsuarioPorId($idUsuario);
        if (!$usuario instanceof Usuario) {
            $this->sessao->destruir();
            throw new AutenticacaoException('É necessário entrar no sistema.');
        }

        return $usuario;
    }
}
