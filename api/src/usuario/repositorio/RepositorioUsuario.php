<?php

interface RepositorioUsuario {
    public function obterUsuarioPorId(int $idUsuario): ?Usuario;
}
