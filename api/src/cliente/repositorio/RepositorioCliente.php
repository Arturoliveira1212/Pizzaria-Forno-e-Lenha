<?php

interface RepositorioCliente {
    /**
     * @return Cliente[]
     */
    public function obterClientes(FiltroCliente $filtro): array;

    public function obterClientePorId(string $idCliente): ?Cliente;
}
