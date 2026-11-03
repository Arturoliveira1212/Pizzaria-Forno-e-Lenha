<?php

interface RepositorioRelatorio {
    /**
     * @return TotalPedidosDia[]
     */
    public function obterTotalPedidosPorDia(PeriodoRelatorio $periodo): array;

    /**
     * @return ValorVendidoItem[]
     */
    public function obterValorVendidoPorItem(PeriodoRelatorio $periodo): array;
}
