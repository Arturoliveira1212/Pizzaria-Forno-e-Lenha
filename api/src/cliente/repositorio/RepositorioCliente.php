<?php

interface RepositorioCliente {
    /**
     * @return Cliente[]
     */
    public function obterClientes(FiltroCliente $filtro): array;

    public function obterClientePorId(string $idCliente): ?Cliente;

    public function marcarBeneficioFidelidadeComoUtilizado(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void;

    public function registrarBeneficioFidelidade(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void;
}
