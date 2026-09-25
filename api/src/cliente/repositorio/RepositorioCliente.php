<?php

interface RepositorioCliente {
    /**
     * @return Cliente[]
     */
    public function obterClientes(FiltroCliente $filtro): array;
}
