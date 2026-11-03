<?php

class Autorizacao {
    private const ACESSOS = [
        'ADMINISTRADOR' => ['pedidos', 'relatorios'],
        'ATENDENTE' => ['pedidos'],
    ];

    public function __construct(
        private readonly Sessao $sessao,
    ) {}

    public function possuiAcesso(string $operacao): bool {
        $this->sessao->iniciar();
        if (!is_int($this->sessao->obter(GestorAutenticacao::CHAVE_ID_USUARIO))) {
            throw new AutenticacaoException('É necessário entrar no sistema.');
        }

        $papel = $this->sessao->obter(GestorAutenticacao::CHAVE_PAPEL);

        return in_array($operacao, self::ACESSOS[$papel] ?? [], true);
    }
}
