import { API } from '../../infra/constantes';
import { HttpError } from '../../infra/exceptions/http-error';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { ServicoHttp } from '../../infra/http/servico-http';
import { DistribuicaoItem } from '../dominio/distribuicao-item';
import { TotalPedidosDia } from '../dominio/total-pedidos-dia';
import { RepositorioRelatorio } from './repositorio-relatorio';

export class RepositorioRelatorioNoServidor implements RepositorioRelatorio {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterTotalPedidosPorDia(dataInicio: string, dataFim: string): Promise<TotalPedidosDia[]> {
		try {
			const parametros = new URLSearchParams({ dataInicio, dataFim });
			const resultado = await this.servicoHttp.get(`${API}/relatorios/pedidos-por-dia?${parametros.toString()}`);

			return resultado.data.totaisPorDia.map((total: { data: string; valorTotal: number }) => new TotalPedidosDia(total.data, total.valorTotal));
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao obter o relatório de pedidos por dia.');
		}
	}

	async obterDistribuicaoItens(dataInicio: string, dataFim: string): Promise<DistribuicaoItem[]> {
		try {
			const parametros = new URLSearchParams({ dataInicio, dataFim });
			const resultado = await this.servicoHttp.get(`${API}/relatorios/distribuicao-itens?${parametros.toString()}`);

			return resultado.data.itens.map((item: { nome: string; valor: number; percentual: number }) => new DistribuicaoItem(item.nome, item.valor, item.percentual));
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao obter o relatório de distribuição de itens.');
		}
	}

	private tratarErro(error: any, mensagemPadrao: string): never {
		const mensagemErro = error instanceof HttpError ? (error.data?.erros?.join('\n') ?? mensagemPadrao) : mensagemPadrao;
		throw new RepositorioError(mensagemErro);
	}
}
