import { ControladoraListagemPedidos } from '../controller/controladora-listagem-pedidos';
import { PedidoListagem } from '../dominio/pedido-listagem';
import { VisaoPedidos } from './visao-pedidos';

export class VisaoListagemPedidos implements VisaoPedidos {
	private readonly pagina = document.querySelector<HTMLElement>('#pagina-listagem-pedidos')!;
	private readonly formularioFiltroPedidos = this.pagina.querySelector<HTMLFormElement>('#formulario-filtro-pedidos')!;
	private readonly inputFiltroPedidos = this.pagina.querySelector<HTMLInputElement>('#filtro-pedidos')!;
	private readonly listaPedidos = this.pagina.querySelector<HTMLElement>('#lista-pedidos')!;
	private readonly modalStatusPedido = this.pagina.querySelector<HTMLElement>('#modal-status-pedido')!;
	private readonly formularioStatusPedido = this.modalStatusPedido.querySelector<HTMLFormElement>('#formulario-status-pedido')!;
	private readonly selectStatusPedido = this.modalStatusPedido.querySelector<HTMLSelectElement>('#novo-status-pedido')!;
	private readonly tituloModalStatus = this.modalStatusPedido.querySelector<HTMLElement>('#titulo-modal-status')!;
	private readonly botaoFecharModalStatus = this.modalStatusPedido.querySelector<HTMLButtonElement>('#botao-fechar-modal-status')!;
	private readonly botaoCancelarModalStatus = this.modalStatusPedido.querySelector<HTMLButtonElement>('#botao-cancelar-modal-status')!;
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
			if (this.pedidoSelecionado !== null) {
				this.controladoraListagemPedidos.alterarStatus(this.pedidoSelecionado.id, this.selectStatusPedido.value);
				this.fecharModalStatus();
			}
		});
		this.botaoFecharModalStatus.addEventListener('click', () => this.fecharModalStatus());
		this.botaoCancelarModalStatus.addEventListener('click', () => this.fecharModalStatus());

		this.controladoraListagemPedidos.obterPedidos();
	}

	exibirPedidos(pedidos: PedidoListagem[]): void {
		this.listaPedidos.innerHTML = '';

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
		const template = document.querySelector<HTMLTemplateElement>('#template-pedido-listagem')!.content.cloneNode(true) as DocumentFragment;
		const linha = template.querySelector('tr')!;

		linha.querySelector<HTMLElement>('.pedido-id')!.textContent = String(pedido.id);
		linha.querySelector<HTMLElement>('.pedido-data')!.textContent = pedido.data;
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

	private abrirModalStatus(pedido: PedidoListagem): void {
		this.pedidoSelecionado = pedido;
		this.tituloModalStatus.textContent = `Pedido #${pedido.id}`;
		this.selectStatusPedido.innerHTML = '';
		pedido.proximosStatus.forEach((status) => {
			const option = document.createElement('option');
			option.value = status;
			option.textContent = status;
			this.selectStatusPedido.appendChild(option);
		});
		this.modalStatusPedido.classList.add('show');
		this.modalStatusPedido.style.display = 'block';
		this.modalStatusPedido.removeAttribute('aria-hidden');
	}

	private fecharModalStatus(): void {
		this.modalStatusPedido.classList.remove('show');
		this.modalStatusPedido.style.display = 'none';
		this.modalStatusPedido.setAttribute('aria-hidden', 'true');
		this.pedidoSelecionado = null;
	}
}
