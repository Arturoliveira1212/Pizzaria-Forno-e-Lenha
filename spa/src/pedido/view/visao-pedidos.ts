import { PedidoListagem } from '../dominio/pedido-listagem';

export interface VisaoPedidos {
	exibirPedidos(pedidos: PedidoListagem[]): void;
	exibirErro(mensagem: string): void;
}
