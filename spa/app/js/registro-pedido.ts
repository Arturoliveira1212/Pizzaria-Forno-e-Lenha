import { VisaoRegistroPedidoDom } from '../../src/pedido/view/visao-registro-pedido-dom';

export default function registroPedido() {
	const visaoRegistroPedido = new VisaoRegistroPedidoDom();
	visaoRegistroPedido.iniciar();
}
