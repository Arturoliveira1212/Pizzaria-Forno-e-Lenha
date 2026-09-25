<?php

class FiltroPizza {
    public const QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA = 100;

    public function __construct(
        public readonly string $identificador, // ID ou nome
        public readonly Paginacao $paginacao,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        $tamanhoIdentificador = mb_strlen($this->identificador);
        if (!empty($this->identificador) && $tamanhoIdentificador > self::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA) {
            $erros[] = 'O identificador deve ter no máximo ' . self::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.';
        }

        if (!empty($erros)) {
            throw new DominioException('Erro ao criar filtro para pizzas.', $erros);
        }
    }
}
