<?php

interface RepositorioCliente {
    /**
     * @return Cliente[]
     */
    public function obterClientesPorNome(string $busca): array;
}
