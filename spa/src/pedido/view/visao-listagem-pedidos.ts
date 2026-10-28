import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { clonarTemplate } from '../../util/dom';
import { ControladoraListagemPedidos } from '../controller/controladora-listagem-pedidos';
import { PedidoListagem } from '../dominio/pedido-listagem';
import { StatusPedido } from '../dominio/status-pedido';
import { VisaoPedidos } from './visao-pedidos';

export class VisaoListagemPedidos implements VisaoPedidos {
	private readonly pagina = document.querySelector<HTMLElement>('#pagina-listagem-pedidos')!;
	private readonly formularioFiltroPedidos = this.pagina.querySelector<HTMLFormElement>('#formulario-filtro-pedidos')!;
	private readonly inputFiltroPedidos = this.pagina.querySelector<HTMLInputElement>('#filtro-pedidos')!;
	private readonly listaPedidos = this.pagina.querySelector<HTMLElement>('#lista-pedidos')!;
	private readonly modalStatusPedido = bootstrap.Modal.getOrCreateInstance('#modal-status-pedido')! as bootstrap.Modal;
	private readonly elementoModalStatusPedido = this.pagina.querySelector<HTMLElement>('#modal-status-pedido')!;
	private readonly formularioStatusPedido = this.elementoModalStatusPedido.querySelector<HTMLFormElement>('#formulario-status-pedido')!;
	private readonly selectStatusPedido = this.elementoModalStatusPedido.querySelector<HTMLSelectElement>('#novo-status-pedido')!;
	private readonly tituloModalStatus = this.elementoModalStatusPedido.querySelector<HTMLElement>('#titulo-modal-status')!;
	private readonly modalCancelamentoPedido = bootstrap.Modal.getOrCreateInstance('#modal-cancelamento-pedido')! as bootstrap.Modal;
	private readonly elementoModalCancelamentoPedido = this.pagina.querySelector<HTMLElement>('#modal-cancelamento-pedido')!;
	private readonly formularioCancelamentoPedido = this.elementoModalCancelamentoPedido.querySelector<HTMLFormElement>('#formulario-cancelamento-pedido')!;
	private readonly textareaMotivoCancelamento = this.elementoModalCancelamentoPedido.querySelector<HTMLTextAreaElement>('#motivo-cancelamento')!;
	private readonly tituloModalCancelamento = this.elementoModalCancelamentoPedido.querySelector<HTMLElement>('#titulo-modal-cancelamento')!;
	private readonly controladoraListagemPedidos: ControladoraListagemPedidos;
	private pedidoSelecionado: PedidoListagem | null = null;

	constructor() {
		this.controladoraListagemPedidos = new ControladoraListagemPedidos(this);
	}

	iniciar(): void {
		this.formularioFiltroPedidos.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraListagemPedidos.obterPedidos(this.inputFiltroPedidos.value.trim());
		});
		this.formularioStatusPedido.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			if (this.pedidoSelecionado === null) {
				return;
			}

			if (this.selectStatusPedido.value === StatusPedido.CANCELADO) {
				this.elementoModalStatusPedido.addEventListener('hidden.bs.modal', () => this.abrirModalCancelamento(), { once: true });
				this.modalStatusPedido.hide();
				return;
			}

			this.controladoraListagemPedidos.alterarStatus(this.pedidoSelecionado.id, this.selectStatusPedido.value);
			this.modalStatusPedido.hide();
		});

		this.formularioCancelamentoPedido.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			if (this.pedidoSelecionado === null) {
				return;
			}

			this.controladoraListagemPedidos.cancelarPedido(this.pedidoSelecionado.id, this.textareaMotivoCancelamento.value.trim());
			this.modalCancelamentoPedido.hide();
		});
		this.elementoModalCancelamentoPedido.addEventListener('shown.bs.modal', () => this.textareaMotivoCancelamento.focus());

		this.controladoraListagemPedidos.obterPedidos();
	}

	exibirPedidos(pedidos: PedidoListagem[]): void {
		this.listaPedidos.replaceChildren();

		if (pedidos.length === 0) {
			this.listaPedidos.innerHTML = '<tr><td colspan="7" class="text-center text-body-secondary">Nenhum pedido encontrado.</td></tr>';
			return;
		}

		const fragmento = document.createDocumentFragment();
		pedidos.forEach((pedido) => fragmento.appendChild(this.criarLinhaPedido(pedido)));
		this.listaPedidos.appendChild(fragmento);
	}

	exibirErro(mensagem: string): void {
		this.listaPedidos.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${mensagem}</td></tr>`;
	}

	private criarLinhaPedido(pedido: PedidoListagem): DocumentFragment {
		const template = clonarTemplate('template-pedido-listagem');
		const linha = template.querySelector('tr')!;

		linha.querySelector<HTMLElement>('.pedido-id')!.textContent = String(pedido.id);
		linha.querySelector<HTMLElement>('.pedido-data')!.textContent = this.formatarData(pedido.data);
		linha.querySelector<HTMLElement>('.pedido-cliente')!.textContent = pedido.nomeCliente ?? 'Consumidor final';
		linha.querySelector<HTMLElement>('.pedido-atendimento')!.textContent = pedido.tipoAtendimento;
		linha.querySelector<HTMLElement>('.pedido-status')!.textContent = pedido.status;
		linha.querySelector<HTMLElement>('.pedido-total')!.textContent = pedido.valorTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
		const botao = linha.querySelector<HTMLButtonElement>('.botao-alterar-status')!;
		if (pedido.proximosStatus.length === 0) {
			botao.disabled = true;
		} else {
			botao.addEventListener('click', () => this.abrirModalStatus(pedido));
		}

		return template;
	}

	private formatarData(data: string): string {
		return new Date(data.replace(' ', 'T')).toLocaleString('pt-BR', {
			dateStyle: 'short',
			timeStyle: 'short',
		});
	}

	private abrirModalStatus(pedido: PedidoListagem): void {
		this.pedidoSelecionado = pedido;
		this.tituloModalStatus.textContent = `Pedido #${pedido.id}`;
		this.selectStatusPedido.replaceChildren();
		pedido.proximosStatus.forEach((status) => {
			const option = document.createElement('option');
			option.value = status;
			option.textContent = status;
			this.selectStatusPedido.appendChild(option);
		});
		this.modalStatusPedido.show();
	}

	private abrirModalCancelamento(): void {
		this.tituloModalCancelamento.textContent = `Cancelar pedido #${this.pedidoSelecionado?.id}`;
		this.textareaMotivoCancelamento.value = '';
		this.modalCancelamentoPedido.show();
	}
}
