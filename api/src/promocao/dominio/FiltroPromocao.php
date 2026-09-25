<?php

class FiltroPromocao {
    public function __construct(
        public readonly bool $apenasVigentes,
        public readonly Paginacao $paginacao,
    ) {}
}
