import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Entregador } from '../../entregador/dominio/entregador';
import { formatarDinheiro } from '../../util/dinheiro';
import { clonarTemplate } from '../../util/dom';
import { ControladoraListagemPedidos } from '../controller/controladora-listagem-pedidos';
import { DetalhesItemPedido, DetalhesPedido, DetalhesSaborEscolhido } from '../dominio/detalhes-pedido';
import { PedidoListagem } from '../dominio/pedido-listagem';
import { StatusPedido } from '../dominio/status-pedido';
import { VisaoDetalhesPedido } from './visao-detalhes-pedido';
import { VisaoPedidos } from './visao-pedidos';
import { VisaoSaidaParaEntrega } from './visao-saida-para-entrega';

export class VisaoListagemPedidos implements VisaoPedidos, VisaoSaidaParaEntrega, VisaoDetalhesPedido {
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
	private readonly modalSaidaParaEntrega = bootstrap.Modal.getOrCreateInstance('#modal-saida-para-entrega')! as bootstrap.Modal;
	private readonly elementoModalSaidaParaEntrega = this.pagina.querySelector<HTMLElement>('#modal-saida-para-entrega')!;
	private readonly formularioSaidaParaEntrega = this.elementoModalSaidaParaEntrega.querySelector<HTMLFormElement>('#formulario-saida-para-entrega')!;
	private readonly selectEntregador = this.elementoModalSaidaParaEntrega.querySelector<HTMLSelectElement>('#entregador-saida-para-entrega')!;
	private readonly tituloModalSaidaParaEntrega = this.elementoModalSaidaParaEntrega.querySelector<HTMLElement>('#titulo-modal-saida-para-entrega')!;
	private readonly modalDetalhesPedido = bootstrap.Modal.getOrCreateInstance('#modal-detalhes-pedido')! as bootstrap.Modal;
	private readonly elementoModalDetalhesPedido = this.pagina.querySelector<HTMLElement>('#modal-detalhes-pedido')!;
	private readonly tituloModalDetalhes = this.elementoModalDetalhesPedido.querySelector<HTMLElement>('#titulo-modal-detalhes')!;
	private readonly avisoDetalhesPedido = this.elementoModalDetalhesPedido.querySelector<HTMLElement>('#aviso-detalhes-pedido')!;
	private readonly conteudoDetalhesPedido = this.elementoModalDetalhesPedido.querySelector<HTMLElement>('#conteudo-detalhes-pedido')!;
	private readonly listaItensDetalhesPedido = this.elementoModalDetalhesPedido.querySelector<HTMLElement>('#lista-itens-detalhes-pedido')!;
	private readonly controladoraListagemPedidos: ControladoraListagemPedidos;
	private pedidoSelecionado: PedidoListagem | null = null;

	constructor() {
		this.controladoraListagemPedidos = new ControladoraListagemPedidos(this, this, this);
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

			if (this.selectStatusPedido.value === StatusPedido.SAIU_PARA_ENTREGA) {
				this.elementoModalStatusPedido.addEventListener('hidden.bs.modal', () => this.abrirModalSaidaParaEntrega(), { once: true });
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

			this.controladoraListagemPedidos.alterarStatus(this.pedidoSelecionado.id, StatusPedido.CANCELADO, this.textareaMotivoCancelamento.value.trim());
			this.modalCancelamentoPedido.hide();
		});
		this.elementoModalCancelamentoPedido.addEventListener('shown.bs.modal', () => this.textareaMotivoCancelamento.focus());

		this.formularioSaidaParaEntrega.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			if (this.pedidoSelecionado === null) {
				return;
			}

			this.controladoraListagemPedidos.registrarSaidaParaEntrega(this.pedidoSelecionado.id, Number(this.selectEntregador.value));
			this.modalSaidaParaEntrega.hide();
		});

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
		linha.querySelector<HTMLButtonElement>('.botao-detalhes-pedido')!.addEventListener('click', () => this.abrirModalDetalhes(pedido));
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

	private abrirModalSaidaParaEntrega(): void {
		this.tituloModalSaidaParaEntrega.textContent = `Saída para entrega do pedido #${this.pedidoSelecionado?.id}`;
		this.selectEntregador.replaceChildren(this.criarOpcaoEntregador('', 'Carregando entregadores...'));
		this.modalSaidaParaEntrega.show();
		this.controladoraListagemPedidos.obterEntregadores();
	}

	exibirEntregadores(entregadores: Entregador[]): void {
		const opcoesEntregadores = entregadores.map((entregador) => this.criarOpcaoEntregador(String(entregador.id), entregador.nome));
		this.selectEntregador.replaceChildren(this.criarOpcaoEntregador('', 'Selecione o entregador'), ...opcoesEntregadores);
	}

	exibirErroAoObterEntregadores(mensagem: string): void {
		this.selectEntregador.replaceChildren(this.criarOpcaoEntregador('', mensagem));
	}

	private abrirModalDetalhes(pedido: PedidoListagem): void {
		this.tituloModalDetalhes.textContent = `Pedido #${pedido.id}`;
		this.avisoDetalhesPedido.textContent = 'Carregando detalhes do pedido...';
		this.avisoDetalhesPedido.classList.remove('d-none', 'text-danger');
		this.conteudoDetalhesPedido.classList.add('d-none');
		this.modalDetalhesPedido.show();
		this.controladoraListagemPedidos.obterDetalhesPedido(pedido.id);
	}

	exibirDetalhesPedido(detalhesPedido: DetalhesPedido): void {
		this.preencherDetalhe('#detalhes-data', this.formatarData(detalhesPedido.data));
		this.preencherDetalhe('#detalhes-atendimento', detalhesPedido.tipoAtendimento);
		this.preencherDetalhe('#detalhes-status', detalhesPedido.status);

		this.exibirSecaoDetalhes('#secao-detalhes-cliente', detalhesPedido.nomeCliente !== null);
		this.preencherDetalhe('#detalhes-nome-cliente', detalhesPedido.nomeCliente ?? '');
		this.preencherDetalhe('#detalhes-telefone-cliente', detalhesPedido.telefoneCliente ?? '');
		this.preencherDetalhe('#detalhes-endereco', detalhesPedido.endereco ?? '');

		this.listaItensDetalhesPedido.replaceChildren(...detalhesPedido.itens.map((item) => this.criarItemDetalhesPedido(item)));

		this.preencherDetalhe('#detalhes-subtotal', formatarDinheiro(detalhesPedido.subtotal));
		this.exibirSecaoDetalhes('.secao-detalhes-promocao', detalhesPedido.valorDescontoPromocao > 0);
		this.preencherDetalhe('#detalhes-desconto-promocao', `-${formatarDinheiro(detalhesPedido.valorDescontoPromocao)}`);
		this.exibirSecaoDetalhes('.secao-detalhes-fidelidade', detalhesPedido.valorDescontoFidelidade > 0);
		this.preencherDetalhe('#detalhes-desconto-fidelidade', `-${formatarDinheiro(detalhesPedido.valorDescontoFidelidade)}`);
		this.exibirSecaoDetalhes('.secao-detalhes-taxa-entrega', detalhesPedido.taxaEntrega > 0);
		this.preencherDetalhe('#detalhes-taxa-entrega', formatarDinheiro(detalhesPedido.taxaEntrega));
		this.preencherDetalhe('#detalhes-valor-total', formatarDinheiro(detalhesPedido.valorTotal));

		this.exibirSecaoDetalhes('#secao-detalhes-entrega', detalhesPedido.nomeEntregador !== null);
		this.preencherDetalhe('#detalhes-entregador', detalhesPedido.nomeEntregador ?? '');
		this.preencherDetalhe('#detalhes-data-saida', detalhesPedido.dataSaida ? this.formatarData(detalhesPedido.dataSaida) : '-');
		this.preencherDetalhe('#detalhes-data-chegada', detalhesPedido.dataChegada ? this.formatarData(detalhesPedido.dataChegada) : '-');

		this.exibirSecaoDetalhes('#secao-detalhes-cancelamento', detalhesPedido.motivoCancelamento !== null);
		this.preencherDetalhe('#detalhes-motivo-cancelamento', detalhesPedido.motivoCancelamento ?? '');
		this.preencherDetalhe('#detalhes-data-cancelamento', detalhesPedido.dataCancelamento ? this.formatarData(detalhesPedido.dataCancelamento) : '');

		this.avisoDetalhesPedido.classList.add('d-none');
		this.conteudoDetalhesPedido.classList.remove('d-none');
	}

	exibirErroAoObterDetalhesPedido(mensagem: string): void {
		this.avisoDetalhesPedido.textContent = mensagem;
		this.avisoDetalhesPedido.classList.add('text-danger');
		this.avisoDetalhesPedido.classList.remove('d-none');
		this.conteudoDetalhesPedido.classList.add('d-none');
	}

	private criarItemDetalhesPedido(item: DetalhesItemPedido): DocumentFragment {
		const template = clonarTemplate('template-item-detalhes-pedido');

		template.querySelector<HTMLElement>('.quantidade-item-detalhes')!.textContent = String(item.quantidade);
		template.querySelector<HTMLElement>('.descricao-item-detalhes')!.textContent = item.descricao;
		template.querySelector<HTMLElement>('.preco-item-detalhes')!.textContent = formatarDinheiro(item.precoTotal);

		const listaSabores = template.querySelector<HTMLElement>('.sabores-item-detalhes')!;
		item.sabores.forEach((sabor) => {
			const linhaSabor = document.createElement('li');
			linhaSabor.textContent = this.descreverSabor(sabor);
			listaSabores.appendChild(linhaSabor);
		});

		return template;
	}

	private descreverSabor(sabor: DetalhesSaborEscolhido): string {
		const alteracoes = [
			...sabor.ingredientesRemovidos.map((ingrediente) => `sem ${ingrediente}`),
			...sabor.ingredientesSubstitutos.map((ingrediente) => `substituto ${ingrediente}`),
			...sabor.adicionais.map((adicional) => `adicional ${adicional.quantidade}x ${adicional.nome}`),
		];

		if (alteracoes.length === 0) {
			return `${sabor.nome}: sem alterações`;
		}

		return `${sabor.nome}: ${alteracoes.join(' · ')}`;
	}

	private preencherDetalhe(seletor: string, texto: string): void {
		this.conteudoDetalhesPedido.querySelector<HTMLElement>(seletor)!.textContent = texto;
	}

	private exibirSecaoDetalhes(seletor: string, exibir: boolean): void {
		this.conteudoDetalhesPedido.querySelectorAll<HTMLElement>(seletor).forEach((elemento) => elemento.classList.toggle('d-none', !exibir));
	}

	private criarOpcaoEntregador(valor: string, texto: string): HTMLOptionElement {
		const option = document.createElement('option');
		option.value = valor;
		option.textContent = texto;

		return option;
	}
}
