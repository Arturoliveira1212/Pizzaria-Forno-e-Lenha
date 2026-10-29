<?php

class RepositorioUsuarioEmBDR implements RepositorioUsuario {
    public function obterUsuarioPorId(int $idUsuario): ?Usuario {
        return UsuarioFactory::atendente(); // TODO: Temporário
    }
}
