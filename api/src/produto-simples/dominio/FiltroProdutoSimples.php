<?php

class FiltroProdutoSimples {
    public const QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA = 100;

    public readonly ?TipoProdutoSimples $tipo;

    public function __construct(
        ?string $tipoEmString,
        public readonly string $identificador, // ID ou nome
        public readonly ?Paginacao $paginacao = null,
    ) {
        $this->tipo = $tipoEmString !== null
            ? TipoProdutoSimples::tryFrom($tipoEmString)
            : null;
        $this->validar($tipoEmString);
    }

    protected function validar(?string $tipoEmString): void {
        $erros = [];

        $restricaoPorTipoFoiEnviada = $tipoEmString !== null;
        if ($restricaoPorTipoFoiEnviada && $this->tipo === null) {
            $erros[] = 'Tipo de produto inválido.';
        }

        $tamanhoIdentificador = mb_strlen($this->identificador);
        if (!empty($this->identificador) && $tamanhoIdentificador > self::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA) {
            $erros[] = 'O identificador deve ter no máximo ' . self::QUANTIDADE_MAXIMA_DE_CARACTERES_PARA_BUSCA . ' caracteres.';
        }

        if (!empty($erros)) {
            throw new DominioException('Erro ao criar filtro para produtos simples.', $erros);
        }
    }
}
