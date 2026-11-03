import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { RepositorioRelatorioNoServidor } from '../repositorio/repositorio-relatorio-no-servidor';
import { VisaoRelatorios } from '../view/visao-relatorios';

export class ControladoraRelatorios {
	private readonly repositorioRelatorio: RepositorioRelatorioNoServidor;

	constructor(private readonly visaoRelatorios: VisaoRelatorios) {
		this.repositorioRelatorio = new RepositorioRelatorioNoServidor(new ServicoHttpComFetch());
	}

	async obterTotalPedidosPorDia(dataInicio: string, dataFim: string): Promise<void> {
		try {
			const totaisPorDia = await this.repositorioRelatorio.obterTotalPedidosPorDia(dataInicio, dataFim);
			this.visaoRelatorios.exibirTotalPedidosPorDia(totaisPorDia);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter o relatório de pedidos por período.';
			this.visaoRelatorios.exibirErro(mensagem);
		}
	}

	async obterDistribuicaoItens(dataInicio: string, dataFim: string): Promise<void> {
		try {
			const itens = await this.repositorioRelatorio.obterDistribuicaoItens(dataInicio, dataFim);
			this.visaoRelatorios.exibirDistribuicaoItens(itens);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter o relatório de distribuição de pedidos.';
			this.visaoRelatorios.exibirErro(mensagem);
		}
	}
}
