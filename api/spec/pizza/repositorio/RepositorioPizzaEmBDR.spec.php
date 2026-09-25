<?php

describe('RepositorioPizzaEmBDR', function () {
    beforeAll(function () {
        exec('php vendor/bin/phinx migrate -e testing');
        exec('php vendor/bin/phinx seed:run -e testing');

        $this->bancoDados = new BancoDados(PDOFactory::criar(
            nomeBanco: $_ENV['DB_NAME_TEST'],
            host: $_ENV['DB_HOST_TEST'],
            usuario: $_ENV['DB_USERNAME_TEST'],
            senha: $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioPizza = new RepositorioPizzaEmBDR($this->bancoDados);
    });
});
