<?php

class Telefone implements JsonSerializable {
    public const TAMANHO_NUMERO_DE_TELEFONE_FIXO = 10;
    public const TAMANHO_NUMERO_DE_TELEFONE_CELULAR = 11;

    public function __construct(
        public readonly string $ddd,
        public readonly string $numero,
    ) {}

    public static function criar(string $telefone): self {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);

        $tamanho = mb_strlen($telefone);
        if ($tamanho != self::TAMANHO_NUMERO_DE_TELEFONE_FIXO && $tamanho != self::TAMANHO_NUMERO_DE_TELEFONE_CELULAR) {
            throw DominioException::criarException('O telefone deve ter entre ' . self::TAMANHO_NUMERO_DE_TELEFONE_FIXO . ' e ' . self::TAMANHO_NUMERO_DE_TELEFONE_CELULAR . ' dígitos.');
        }

        $ddd = substr($telefone, 0, 2);
        $numero = substr($telefone, 2);

        return new self($ddd, $numero);
    }

    public function formatar(): string {
        return "({$this->ddd}) {$this->numero}";
    }

    public function jsonSerialize(): mixed {
        return $this->formatar();
    }
}
