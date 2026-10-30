import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { RepositorioEntregadorNoServidor } from '../../entregador/repositorio/repositorio-entregador-no-servidor';
import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { VisaoPedidos } from '../view/visao-pedidos';
import { VisaoSaidaParaEntrega } from '../view/visao-saida-para-entrega';
import { VisaoDetalhesPedido } from '../view/visao-detalhes-pedido';

export class ControladoraListagemPedidos {
	private readonly repositorioPedido: RepositorioPedidoNoServidor;
	private readonly repositorioEntregador: RepositorioEntregadorNoServidor;

	constructor(
		private readonly visaoListagemPedidos: VisaoPedidos,
		private readonly visaoSaidaParaEntrega?: VisaoSaidaParaEntrega,
		private readonly visaoDetalhesPedido?: VisaoDetalhesPedido,
	) {
		const servicoHttp = new ServicoHttpComFetch();
		this.repositorioPedido = new RepositorioPedidoNoServidor(servicoHttp);
		this.repositorioEntregador = new RepositorioEntregadorNoServidor(servicoHttp);
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

	async obterDetalhesPedido(idPedido: number) {
		try {
			const detalhesPedido = await this.repositorioPedido.obterDetalhesPedido(idPedido);
			this.visaoDetalhesPedido?.exibirDetalhesPedido(detalhesPedido);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter os detalhes do pedido.';
			this.visaoDetalhesPedido?.exibirErroAoObterDetalhesPedido(mensagemErro);
		}
	}

	async obterEntregadores() {
		try {
			const entregadores = await this.repositorioEntregador.obterEntregadores();
			this.visaoSaidaParaEntrega?.exibirEntregadores(entregadores);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter os entregadores.';
			this.visaoSaidaParaEntrega?.exibirErroAoObterEntregadores(mensagemErro);
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

	async registrarSaidaParaEntrega(idPedido: number, idEntregador: number): Promise<void> {
		try {
			await this.repositorioPedido.registrarSaidaParaEntrega(idPedido, idEntregador);
			await this.obterPedidos();
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao registrar a saída para entrega do pedido.';
			this.visaoListagemPedidos.exibirErro(mensagem);
		}
	}

	async cancelarPedido(idPedido: number, motivo: string): Promise<void> {
		try {
			await this.repositorioPedido.cancelarPedido(idPedido, motivo);
			await this.obterPedidos();
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao cancelar o pedido.';
			this.visaoListagemPedidos.exibirErro(mensagem);
		}
	}
}
