<?php

interface RepositorioPromocao {
    /**
     * @return Promocao[]
     */
    public function obterPromocoes(FiltroPromocao $filtro): array;

    public function obterPromocaoPorId(int $id): ?Promocao;
}
