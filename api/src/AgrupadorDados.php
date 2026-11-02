<?php

class AgrupadorDados {
    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<int, mixed[]>
     */
    public static function agruparPor(array $linhas, string $chave, callable $criarObjeto, mixed ...$parametros): array {
        $agrupado = [];

        foreach ($linhas as $linha) {
            $id = (int) $linha[$chave];
            $agrupado[$id][] = $criarObjeto($linha, ...$parametros);
        }

        return $agrupado;
    }
}
