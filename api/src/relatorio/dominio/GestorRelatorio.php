<?php

class GestorRelatorio {
    public function __construct(
        private readonly Autorizacao $autorizacao,
        private readonly RepositorioRelatorio $repositorioRelatorio,
    ) {}

    /**
     * @return TotalPedidosDia[]
     */
    public function obterTotalPedidosPorDia(PeriodoParaRelatorio $periodoParaRelatorio): array {
        $this->verificarAcesso();

        return $this->repositorioRelatorio->obterTotalPedidosPorDia($this->criarPeriodo($periodoParaRelatorio));
    }

    /**
     * @return DistribuicaoItem[]
     */
    public function obterDistribuicaoItens(PeriodoParaRelatorio $periodoParaRelatorio): array {
        $this->verificarAcesso();

        $valoresVendidos = $this->repositorioRelatorio->obterValorVendidoPorItem($this->criarPeriodo($periodoParaRelatorio));

        $totalVendido = Dinheiro::zero();
        foreach ($valoresVendidos as $valorVendido) {
            $totalVendido = $totalVendido->somar($valorVendido->valor);
        }

        return array_map(
            fn(ValorVendidoItem $valorVendido): DistribuicaoItem => new DistribuicaoItem(
                nome: $valorVendido->nome,
                valor: $valorVendido->valor,
                percentual: $totalVendido->centavos > 0 ? round($valorVendido->valor->centavos * 100 / $totalVendido->centavos, 2) : 0.0,
            ),
            $valoresVendidos,
        );
    }

    private function verificarAcesso(): void {
        if (!$this->autorizacao->possuiAcesso('relatorios')) {
            throw new SemAcessoException('Somente administradores podem acessar os relatórios.');
        }
    }

    private function criarPeriodo(PeriodoParaRelatorio $periodoParaRelatorio): PeriodoRelatorio {
        $dataInicio = DateTime::createFromFormat('!Y-m-d', $periodoParaRelatorio->dataInicio);
        $dataFim = DateTime::createFromFormat('!Y-m-d', $periodoParaRelatorio->dataFim);

        $erros = [];
        if ($dataInicio === false) {
            $erros[] = 'A data de início é inválida.';
        }

        if ($dataFim === false) {
            $erros[] = 'A data de fim é inválida.';
        }

        if ($dataInicio === false || $dataFim === false) {
            throw DominioException::criarException(...$erros);
        }

        return new PeriodoRelatorio(
            dataInicio: $dataInicio,
            dataFim: $dataFim,
        );
    }
}
