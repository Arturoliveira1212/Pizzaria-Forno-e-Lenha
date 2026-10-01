<?php

interface UnidadeTrabalho {
    public function executar(callable $operacao): void;
}
