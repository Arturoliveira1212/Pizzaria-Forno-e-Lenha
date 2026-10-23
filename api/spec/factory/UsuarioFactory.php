<?php

class UsuarioFactory {
    public static function atendente(): Usuario {
        return new Usuario(
            id: 1,
            nome: 'Atendente',
            cpf: new CPF('12345678901'),
            senha: 'asddasdasdasdasdasdas',
            papel: PapelUsuario::ATENDENTE,
        );
    }
}
