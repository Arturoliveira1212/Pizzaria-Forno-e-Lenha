<?php

class PDOFactory {
    public static function criar(
        string $nomeBanco,
        string $host,
        string $usuario,
        string $senha,
    ): PDO {
        return new PDO(
            "mysql:dbname={$nomeBanco};host={$host};charset=utf8",
            $usuario,
            $senha,
        );
    }
}
