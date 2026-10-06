import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { VisaoListagemPedidos } from '../view/visao-listagem-pedidos';
import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';

export class ControladoraListagemPedidos {
	private readonly repositorioPedido: RepositorioPedidoNoServidor;

	constructor(private readonly visaoListagemPedidos: VisaoListagemPedidos) {
		this.repositorioPedido = new RepositorioPedidoNoServidor(new ServicoHttpComFetch());
	}

	async obterPedidos(identificador: string = ''): Promise<void> {
		try {
			const pedidos = await this.repositorioPedido.obterPedidos(identificador);
			this.visaoListagemPedidos.exibirPedidos(pedidos);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter os pedidos.';
			this.visaoListagemPedidos.exibirErro(mensagem);
		}
	}

	async alterarStatus(idPedido: number, status: string): Promise<void> {
		try {
			await this.repositorioPedido.alterarStatus(idPedido, status);
			await this.obterPedidos();
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao alterar o status do pedido.';
			this.visaoListagemPedidos.exibirErro(mensagem);
		}
	}
}
