import { DistribuicaoItem } from '../dominio/distribuicao-item';
import { TotalPedidosDia } from '../dominio/total-pedidos-dia';

export interface VisaoRelatorios {
	exibirTotalPedidosPorDia(totaisPorDia: TotalPedidosDia[]): void;

	exibirDistribuicaoItens(itens: DistribuicaoItem[]): void;

	exibirErro(mensagem: string): void;
}
