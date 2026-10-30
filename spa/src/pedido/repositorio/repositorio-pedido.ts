import { Pedido } from '../dominio/pedido';
import { ResumoValoresPedido } from '../dominio/resumo-valores-pedido';
import { PedidoListagem } from '../dominio/pedido-listagem';
import { DetalhesPedido } from '../dominio/detalhes-pedido';

export interface RepositorioPedido {
	obterPedidos(identificador?: string): Promise<PedidoListagem[]>;

	obterDetalhesPedido(idPedido: number): Promise<DetalhesPedido>;

	alterarStatus(idPedido: number, status: string): Promise<void>;

	cancelarPedido(idPedido: number, motivo: string): Promise<void>;

	registrarSaidaParaEntrega(idPedido: number, idEntregador: number): Promise<void>;

	obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido>;

	registrarPedido(pedido: Pedido): Promise<void>;
}
