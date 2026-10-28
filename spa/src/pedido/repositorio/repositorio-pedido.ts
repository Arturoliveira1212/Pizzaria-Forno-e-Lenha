import { Pedido } from '../dominio/pedido';
import { ResumoValoresPedido } from '../dominio/resumo-valores-pedido';
import { PedidoListagem } from '../dominio/pedido-listagem';

export interface RepositorioPedido {
	obterPedidos(identificador?: string): Promise<PedidoListagem[]>;

	alterarStatus(idPedido: number, status: string): Promise<void>;

	cancelarPedido(idPedido: number, motivo: string): Promise<void>;

	obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido>;

	registrarPedido(pedido: Pedido): Promise<void>;
}
