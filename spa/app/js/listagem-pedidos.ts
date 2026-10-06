import { VisaoListagemPedidos } from '../../src/pedido/view/visao-listagem-pedidos';

export default function listagemPedidos() {
	const visaoListagemPedidos = new VisaoListagemPedidos();
	visaoListagemPedidos.iniciar();
}
