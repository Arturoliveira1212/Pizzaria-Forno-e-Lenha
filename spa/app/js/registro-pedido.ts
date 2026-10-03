import { VisaoRegistroPedido } from '../../src/pedido/view/visao-registro-pedido';

export default function registroPedido() {
	const visaoRegistroPedido = new VisaoRegistroPedido();
	visaoRegistroPedido.iniciar();
}
