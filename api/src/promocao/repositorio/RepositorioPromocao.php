<?php

interface RepositorioPromocao {
    /**
     * @return Promocao[]
    */
    public function obterPromocoes(FiltroPromocao $filtro): array;
}
