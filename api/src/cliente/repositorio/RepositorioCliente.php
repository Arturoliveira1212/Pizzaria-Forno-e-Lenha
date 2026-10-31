<?php

interface RepositorioCliente {
    /**
     * @return Cliente[]
     */
    public function obterClientes(FiltroCliente $filtro): array;

    public function obterClientePorId(string $idCliente): ?Cliente;

    public function obterBeneficioFidelidadePorId(int $idBeneficioFidelidade): ?BeneficioFidelidade;

    public function marcarBeneficioFidelidadeComoUtilizado(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void;

    public function registrarBeneficioFidelidade(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void;
}
