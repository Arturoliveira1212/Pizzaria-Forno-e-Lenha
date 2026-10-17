import { rotuloTamanho } from '../../pizza/dominio/tamanho-pizza';
import { formatarDinheiro } from '../../util/dinheiro';
import { clonarTemplate } from '../../util/dom';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { ItemPizza } from '../dominio/item-pizza';
import { ItemSimples } from '../dominio/item-simples';
import { Pedido } from '../dominio/pedido';
import { TipoAtendimento } from '../dominio/tipo-atendimento';

export class VisaoResumoPedido {
	private controladoraRegistroPedido!: ControladoraRegistroPedido;

	private readonly listaItensPedido = document.querySelector<HTMLElement>('#lista-itens-pedido')!;
	private readonly tituloCarrinho = document.querySelector<HTMLElement>('#titulo-carrinho')!;
	private readonly avisoCarrinhoVazio = document.querySelector<HTMLElement>('#aviso-vazio')!;
	private readonly botaoRegistrarPedido = document.querySelector<HTMLButtonElement>('#botao-registrar-pedido')!;

	iniciar(controladora: ControladoraRegistroPedido) {
		this.controladoraRegistroPedido = controladora;

		this.registrarEventos();
	}

	private registrarEventos() {
		this.listaItensPedido.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const item = elementoClicado.closest<HTMLElement>('.item-pedido');
			if (!item) {
				return;
			}

			const indice = Number(item.dataset.indice);
			if (elementoClicado.closest('.aumentar-quantidade')) {
				this.controladoraRegistroPedido.alterarQuantidade(indice, 1);
			} else if (elementoClicado.closest('.diminuir-quantidade')) {
				this.controladoraRegistroPedido.alterarQuantidade(indice, -1);
			} else if (elementoClicado.closest('.remover-item')) {
				this.controladoraRegistroPedido.removerItem(indice);
			}
		});

		this.botaoRegistrarPedido.addEventListener('click', () => {
			this.desabilitarRegistroPedido();
			this.controladoraRegistroPedido.registrarPedido();
		});
	}

	exibirResumoPedido(pedido: Pedido): void {
		const itensPedido = pedido.itensPedido;

		this.listaItensPedido.replaceChildren();
		this.avisoCarrinhoVazio.classList.toggle('d-none', itensPedido.length > 0);

		const totalUnidades = itensPedido.reduce((soma, item) => soma + item.quantidade, 0);
		this.tituloCarrinho.textContent = `Carrinho (${totalUnidades} ${totalUnidades === 1 ? 'item' : 'itens'})`;

		const fragmento = document.createDocumentFragment();
		itensPedido.forEach((item, indice) => {
			if (item instanceof ItemPizza) {
				fragmento.appendChild(this.criarItemPizzaNoResumo(item, indice));
			} else if (item instanceof ItemSimples) {
				fragmento.appendChild(this.criarItemSimplesNoResumo(item, indice));
			}
		});

		this.listaItensPedido.appendChild(fragmento);
		this.atualizarValoresResumo(pedido);
	}

	private criarItemPizzaNoResumo(item: ItemPizza, indice: number): DocumentFragment {
		const template = clonarTemplate('template-item-pedido');
		const elemento = template.querySelector<HTMLElement>('.item-pedido')!;
		elemento.dataset.indice = String(indice);

		const pizzas = item.saboresEscolhidos.map((sabor) => sabor.pizza.nome);

		const divNomeItem = elemento.querySelector<HTMLElement>('.nome-item')!;
		divNomeItem.textContent = `Pizza ${rotuloTamanho(item.tamanhoPizza)}`;

		const divSaboresItem = elemento.querySelector<HTMLElement>('.sabores-item')!;
		divSaboresItem.textContent = pizzas.join(' / ');

		const inputQuantidade = elemento.querySelector<HTMLInputElement>('.quantidade-item')!;
		inputQuantidade.value = String(item.quantidade);

		const divPrecoItem = elemento.querySelector<HTMLElement>('.preco-item')!;
		divPrecoItem.textContent = formatarDinheiro(item.preco);

		return template;
	}

	private criarItemSimplesNoResumo(item: ItemSimples, indice: number): DocumentFragment {
		const template = clonarTemplate('template-item-pedido');
		const elemento = template.querySelector<HTMLElement>('.item-pedido')!;
		elemento.dataset.indice = String(indice);

		const divNomeItem = elemento.querySelector<HTMLElement>('.nome-item')!;
		divNomeItem.textContent = item.nome;

		const divSaboresItem = elemento.querySelector<HTMLElement>('.sabores-item')!;
		divSaboresItem.textContent = '';

		const inputQuantidade = elemento.querySelector<HTMLInputElement>('.quantidade-item')!;
		inputQuantidade.value = String(item.quantidade);

		const divPrecoItem = elemento.querySelector<HTMLElement>('.preco-item')!;
		divPrecoItem.textContent = formatarDinheiro(item.preco);

		return template;
	}

	private atualizarValoresResumo(pedido: Pedido): void {
		const subtotal = pedido.itensPedido.reduce((total, item) => {
			return total + (item instanceof ItemPizza || item instanceof ItemSimples ? item.preco * item.quantidade : 0);
		}, 0);

		const divSubtotal = document.querySelector<HTMLElement>('#subtotal')!;
		divSubtotal.textContent = formatarDinheiro(subtotal);

		const divValorDescontoPromocao = document.querySelector<HTMLElement>('#valor-desconto-promocao')!;
		divValorDescontoPromocao.textContent = `-${formatarDinheiro(pedido.valorDescontoPromocao)}`;

		const divValorDescontoFidelidade = document.querySelector<HTMLElement>('#valor-desconto-fidelidade')!;
		divValorDescontoFidelidade.textContent = `-${formatarDinheiro(pedido.valorDescontoFidelidade)}`;

		const divTaxaEntrega = document.querySelector<HTMLElement>('#valor-entrega')!;
		divTaxaEntrega.textContent = formatarDinheiro(pedido.taxaEntrega);

		const divValorTotal = document.querySelector<HTMLElement>('#valor-total')!;
		divValorTotal.textContent = formatarDinheiro(pedido.valorTotal);

		const secaoEntrega = document.querySelector<HTMLElement>('#secao-entrega')!;
		secaoEntrega.classList.toggle('d-none', pedido.tipoAtendimento !== TipoAtendimento.ENTREGA);

		const secaoPromocoes = document.querySelector<HTMLElement>('#secao-promocoes')!;
		secaoPromocoes.classList.toggle('d-none', pedido.valorDescontoPromocao <= 0);

		const secaoFidelidade = document.querySelector<HTMLElement>('#secao-fidelidade')!;
		secaoFidelidade.classList.toggle('d-none', pedido.valorDescontoFidelidade <= 0);
	}

	exibirErroNoPedido(mensagem: string): void {
		this.tituloCarrinho.querySelector('.alert')?.remove();

		const template = clonarTemplate('template-pedido-erro');
		const mensagemErro = template.querySelector<HTMLElement>('.mensagem')!;
		mensagemErro.textContent = mensagem;

		this.tituloCarrinho.append(template);

		window.setTimeout(() => {
			this.tituloCarrinho.querySelector('.alert')?.remove();
		}, 5000);
	}

	habilitarRegistroPedido(): void {
		this.botaoRegistrarPedido.disabled = false;
	}

	desabilitarRegistroPedido(): void {
		this.botaoRegistrarPedido.disabled = true;
	}

	exibirMensagemPedidoRegistrado(): void {
		const template = clonarTemplate('template-pedido-sucesso');
		const aviso = template.querySelector<HTMLElement>('.mensagem')!;
		aviso.textContent = 'Pedido registrado com sucesso.';
		this.tituloCarrinho.append(template);
	}
}
