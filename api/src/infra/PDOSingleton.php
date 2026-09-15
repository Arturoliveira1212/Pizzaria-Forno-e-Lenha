<?php

class PDOSingleton {
    private static ?PDO $pdo = null;

    private function __construct() {
    }

    private static function conectar(): PDO {
        return new PDO(
            "mysql:dbname={$_ENV['DB_NAME']};host={$_ENV['DB_HOST']};charset=utf8",
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
        );
    }

    public static function get(): PDO {
        if (!self::$pdo instanceof PDO) {
            self::$pdo = self::conectar();
        }

        return self::$pdo;
    }
}