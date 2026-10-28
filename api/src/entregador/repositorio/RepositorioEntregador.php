<?php

interface RepositorioEntregador {
    /**
     * @return Entregador[]
     */
    public function obterEntregadoresDisponiveis(): array;

    public function obterEntregadorPorId(int $idEntregador): ?Entregador;
}
