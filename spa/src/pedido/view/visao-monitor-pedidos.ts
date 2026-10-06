import { ControladoraListagemPedidos } from '../controller/controladora-listagem-pedidos';
import { PedidoListagem } from '../dominio/pedido-listagem';
import { VisaoPedidos } from './visao-pedidos';

export class VisaoMonitorPedidos implements VisaoPedidos {
	private readonly pagina = document.querySelector<HTMLElement>('#pagina-monitor-pedidos')!;
	private readonly listaPedidos = this.pagina.querySelector<HTMLElement>('#lista-pedidos-monitor')!;
	private readonly controladoraListagemPedidos: ControladoraListagemPedidos;
	private intervaloAtualizacao: number | undefined;

	constructor() {
		this.controladoraListagemPedidos = new ControladoraListagemPedidos(this);
	}

	iniciar(): void {
		this.controladoraListagemPedidos.obterPedidos();
		this.intervaloAtualizacao = window.setInterval(() => this.controladoraListagemPedidos.obterPedidos(), 15000);
	}

	parar(): void {
		if (this.intervaloAtualizacao !== undefined) {
			window.clearInterval(this.intervaloAtualizacao);
		}
	}

	exibirPedidos(pedidos: PedidoListagem[]): void {
		this.listaPedidos.innerHTML = '';

		if (pedidos.length === 0) {
			this.listaPedidos.innerHTML = '<tr><td colspan="3" class="text-center text-body-secondary">Nenhum pedido no momento.</td></tr>';
			return;
		}

		const fragmento = document.createDocumentFragment();
		pedidos.forEach((pedido) => fragmento.appendChild(this.criarLinhaPedido(pedido)));
		this.listaPedidos.appendChild(fragmento);
	}

	exibirErro(mensagem: string): void {
		this.listaPedidos.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${mensagem}</td></tr>`;
	}

	private criarLinhaPedido(pedido: PedidoListagem): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-pedido-monitor')!.content.cloneNode(true) as DocumentFragment;
		const linha = template.querySelector('tr')!;

		linha.querySelector<HTMLElement>('.pedido-id')!.textContent = `#${pedido.id}`;
		linha.querySelector<HTMLElement>('.pedido-atendimento')!.textContent = pedido.tipoAtendimento;
		linha.querySelector<HTMLElement>('.pedido-status')!.textContent = pedido.status;

		return template;
	}
}
