import { RepositorioPizzaNoServidor } from '../../pizza/repositorio/repositorio-pizza-no-servidor';
import { GestorPedido } from '../dominio/gestor-pedido';
import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { VisaoRegistroPedido } from '../view/visao-registro-pedido';
import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';

export class ControladoraRegistroPedido {
	private readonly gestorPedido: GestorPedido;
	private readonly visaoRegistroPedido: VisaoRegistroPedido;

	constructor(visaoRegistroPedido: VisaoRegistroPedido) {
		this.visaoRegistroPedido = visaoRegistroPedido;

		const servicoHttp = new ServicoHttpComFetch();
		this.gestorPedido = new GestorPedido(
			new RepositorioPedidoNoServidor(servicoHttp),
			new RepositorioPizzaNoServidor(servicoHttp),
		);
	}
}
