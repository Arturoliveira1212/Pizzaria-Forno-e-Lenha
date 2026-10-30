<?php

interface RepositorioUsuario {
    public function obterUsuarioPorId(int $idUsuario): ?Usuario;

    public function obterUsuarioPorCpf(string $cpf): ?Usuario;
}
