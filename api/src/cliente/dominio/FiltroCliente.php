<?php

class FiltroCliente {
    public const QUANTIDADE_MINIMA_DE_CARACTERES_PARA_BUSCA = 3;
    public const QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA = 100;
    public const LIMITE_PADRAO = 15;

    public function __construct(
        public readonly string $identificador, // Nome ou CPF
        public readonly ?Paginacao $paginacao = null,
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

        if (!empty($erros)) {
            throw new DominioException('Erro ao criar filtro para clientes.', $erros);
        }
    }
}
