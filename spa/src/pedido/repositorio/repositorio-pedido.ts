import { Pedido } from '../dominio/pedido';
import { ResumoValoresPedido } from '../dominio/resumo-valores-pedido';

export interface RepositorioPedido {
	obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido>;
	registrarPedido(pedido: Pedido): Promise<void>;
}
