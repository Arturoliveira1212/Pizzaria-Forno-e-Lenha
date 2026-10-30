<?php

class UsuarioLogadoFixo implements ProvedorUsuarioLogado {
    public function __construct(
        private readonly Usuario $usuario,
    ) {}

    public function obterUsuarioLogado(): Usuario {
        return $this->usuario;
    }
}
