import { Pedido } from '../../pedido/dominio/pedido';

export class PedidoAtualizadoError extends Error {
	constructor(
		mensagem: string,
		public readonly pedidoAtualizado: Pedido,
	) {
		super(mensagem);
		this.name = 'PedidoAtualizadoError';
		this.pedidoAtualizado = pedidoAtualizado;
	}
}
