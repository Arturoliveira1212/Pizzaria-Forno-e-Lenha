import { GestorPedido } from '../dominio/gestor-pedido';
import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { VisaoRegistroPedido } from '../view/visao-registro-pedido';

export class ControladoraRegistroPedido {
	private readonly gestorPedido: GestorPedido;
	private readonly visaoRegistroPedido: VisaoRegistroPedido;

	constructor(visaoRegistroPedido: VisaoRegistroPedido) {
		this.visaoRegistroPedido = visaoRegistroPedido;
		this.gestorPedido = new GestorPedido(new RepositorioPedidoNoServidor());
	}
}
