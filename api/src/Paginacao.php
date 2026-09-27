<?php

class Paginacao {
    public const PAGINA_PADRAO = 1;
    public const PAGINA_MINIMA = 1;
    public const LIMITE_PADRAO = 50;
    public const LIMITE_MINIMO_PADRAO = 1;
    public const LIMITE_MAXIMO_PADRAO = 100;

    public function __construct(
        public readonly int $pagina = self::PAGINA_PADRAO,
        public readonly int $limite = self::LIMITE_PADRAO,
        public readonly int $limiteMinimo = self::LIMITE_MINIMO_PADRAO,
        public readonly int $limiteMaximo = self::LIMITE_MAXIMO_PADRAO,
    ) {
        $this->validar();
    }

    protected function validar(): void {
        $erros = [];

        if ($this->pagina < self::PAGINA_MINIMA) {
            $erros[] = 'A página deve ser maior ou igual a ' . self::PAGINA_MINIMA . '.';
        }

        if ($this->limite < $this->limiteMinimo) {
            $erros[] = "O limite deve ser maior ou igual a {$this->limiteMinimo}.";
        }

        if ($this->limite > $this->limiteMaximo) {
            $erros[] = "O limite deve ser menor ou igual a {$this->limiteMaximo}.";
        }

        if (!empty($erros)) {
            throw DominioException::criarException(...$erros);
        }
    }
}
