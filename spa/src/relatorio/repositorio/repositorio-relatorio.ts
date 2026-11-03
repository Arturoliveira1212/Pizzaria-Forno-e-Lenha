import { DistribuicaoItem } from '../dominio/distribuicao-item';
import { TotalPedidosDia } from '../dominio/total-pedidos-dia';

export interface RepositorioRelatorio {
	obterTotalPedidosPorDia(dataInicio: string, dataFim: string): Promise<TotalPedidosDia[]>;

	obterDistribuicaoItens(dataInicio: string, dataFim: string): Promise<DistribuicaoItem[]>;
}
