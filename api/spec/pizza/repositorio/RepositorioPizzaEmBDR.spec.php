<?php

describe('RepositorioPizzaEmBDR', function () {
    beforeAll(function () {
        exec('php vendor/bin/phinx migrate -e testing');
        exec('php vendor/bin/phinx seed:run -e testing');

        $this->bancoDados = new BancoDados(PDOFactory::criar(
            $_ENV['DB_NAME_TEST'],
            $_ENV['DB_HOST_TEST'],
            $_ENV['DB_USERNAME_TEST'],
            $_ENV['DB_PASSWORD_TEST'],
        ));

        $this->repositorioPizza = new RepositorioPizzaEmBDR($this->bancoDados);
    });
});
