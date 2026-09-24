<?php

interface RepositorioPromocao {
    /**
     * @return Promocao[]
    */
    public function obterPromocoesAtivasNoMomento(): array;

    /**
     * @return Promocao[]
    */
    public function obterPromocoes(): array;
}
