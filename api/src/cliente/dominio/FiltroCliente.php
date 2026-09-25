<?php

class FiltroCliente {
    public const QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA = 3;
    public const QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA = 100;
    public const PAGINA_PADRAO = 1;
    public const PAGINA_MINIMA = 1;
    public const TAMANHO_PAGINA_PADRAO = 10;
    public const TAMANHO_PAGINA_MINIMO = 1;
    public const TAMANHO_PAGINA_MAXIMO = 100;

    public function __construct(
        public readonly string $identificador, // Nome Ou Cpf
        public readonly int $pagina,
        public readonly int $tamanhoPagina,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        $tamanhoIdentificador = mb_strlen($this->identificador);
        if (
            !empty($this->identificador) && (
                $tamanhoIdentificador < self::QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA
                || $tamanhoIdentificador > self::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA
            )
        ) {
            $erros[] = 'O identificador deve ter entre ' . self::QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA . ' e ' . self::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.';
        }

        if ($this->pagina < self::PAGINA_MINIMA) {
            $erros[] = 'A página deve ser maior ou igual a 1.';
        }

        if ($this->tamanhoPagina < self::TAMANHO_PAGINA_MINIMO) {
            $erros[] = 'O tamanho da página deve ser maior ou igual a 10.';
        }

        if ($this->tamanhoPagina > self::TAMANHO_PAGINA_MAXIMO) {
            $erros[] = 'O tamanho da página deve ser menor ou igual a 100.';
        }

        if (!empty($erros)) {
            throw new DominioException('Erro ao filtrar clientes.', $erros);
        }
    }
}
