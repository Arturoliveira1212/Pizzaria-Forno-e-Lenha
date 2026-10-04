import { Pizza } from '../../pizza/dominio/pizza';
import { PrecoPorTamanho } from '../../pizza/dominio/preco-por-tamanho';
import { rotuloTamanho } from '../../pizza/dominio/tamanho-pizza';
import { ProdutoSimples } from '../../produto-simples/dominio/produto-simples';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { CategoriaCardapio } from '../categoria-cardapio';
import { EtapaPedido } from '../etapa-pedido';
import { FiltroCardapio } from '../filtro-cardapio';
import { TipoAtendimento } from '../dominio/tipo-atendimento';
import { TipoCliente } from '../tipo-cliente';
import { Cliente } from '../../cliente/dominio/cliente';
import { ItemPizza } from '../dominio/item-pizza';
import { ItemSimples } from '../dominio/item-simples';
import { Pedido } from '../dominio/pedido';

export class VisaoRegistroPedido {
	private readonly controladoraRegistroPedido: ControladoraRegistroPedido;
	private readonly menuEtapas = document.querySelector<HTMLElement>('#etapas')!;
	private readonly menuCategoriasCardapio = document.querySelector<HTMLElement>('#categorias-cardapio')!;
	private readonly inputFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLInputElement>('#filtro-cardapio')!;
	private readonly menuTiposAtendimento = document.querySelector<HTMLElement>('#tipos-atendimento')!;
	private readonly menuTiposCliente = document.querySelector<HTMLElement>('#tipos-cliente')!;
	private readonly secaoCliente = document.querySelector<HTMLElement>('#secao-cliente')!;
	private readonly inputFiltroCliente = this.secaoCliente.querySelector<HTMLInputElement>('#filtro-cliente')!;
	private readonly secaoConsumidorFinal = document.querySelector<HTMLElement>('#secao-consumidor-final')!;
	private readonly listaItensPedido = document.querySelector<HTMLElement>('#lista-itens-pedido')!;
	private readonly tituloCarrinho = document.querySelector<HTMLElement>('#titulo-carrinho')!;
	private readonly listaClientes = document.querySelector<HTMLElement>('#lista-clientes')!;

	constructor() {
		this.controladoraRegistroPedido = new ControladoraRegistroPedido(this);
	}

	iniciar() {
		this.registrarEventos();
		this.controladoraRegistroPedido.obterCardapio();
	}

	private registrarEventos() {
		this.menuEtapas.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLElement>('[data-etapa]');
			if (!botao) {
				return;
			}

			const etapa = (botao?.dataset.etapa as EtapaPedido) ?? EtapaPedido.ATENDIMENTO;
			this.exibirEtapa(etapa);
		});

		this.menuCategoriasCardapio.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'categoria-cardapio' || !radio.checked) {
				return;
			}

			const categoria = (radio.value as CategoriaCardapio) ?? CategoriaCardapio.PIZZA;
			this.exibirMenuCategoriasCardapio(categoria);

			this.controladoraRegistroPedido.obterCardapio();
		});

		const formularioFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLElement>('#formulario-filtro-cardapio')!;
		formularioFiltroCardapio.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraRegistroPedido.obterCardapio();
		});

		this.menuTiposAtendimento.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'tipo-atendimento' || !radio.checked) {
				return;
			}

			this.atualizarRestricoesTipoAtendimento();
			this.limparInputEAdicionarFoco(this.inputFiltroCliente);
			this.controladoraRegistroPedido.atualizarTipoAtendimento(radio.value as TipoAtendimento);
		});

		this.menuTiposCliente.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'tipo-cliente' || !radio.checked) {
				return;
			}

			const tipoCliente = (radio.value as TipoCliente) || TipoCliente.CLIENTE_IDENTIFICADO;

			this.atualizarSecaoCliente(tipoCliente);
			this.atualizarRestricoesTipoAtendimento();
			this.limparInputEAdicionarFoco(this.inputFiltroCliente);
		});

		const formularioFiltroCliente = this.secaoCliente.querySelector<HTMLElement>('#formulario-filtro-cliente')!;
		formularioFiltroCliente.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraRegistroPedido.obterClientes();
		});

		const secaoPizza = document.querySelector<HTMLElement>('#secao-pizza')!;
		secaoPizza.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLButtonElement>('.adicionar-item');
			if (!botao) {
				return;
			}

			const card = botao.closest<HTMLElement>('.card-item')!;
			const itemPizza = this.obterDadosDoItemPizza(card);
			if (!itemPizza) {
				return;
			}

			this.controladoraRegistroPedido.adicionarItemPizza(itemPizza);
		});

		const secoesProdutosSimples = ['#secao-bebida', '#secao-sobremesa'];
		secoesProdutosSimples.forEach((seletorSecao) => {
			document.querySelector<HTMLElement>(seletorSecao)!.addEventListener('click', (event: Event) => {
				const elementoClicado = event.target as HTMLElement;
				const botao = elementoClicado.closest<HTMLButtonElement>('.adicionar-item');
				if (!botao) {
					return;
				}

				const card = botao.closest<HTMLElement>('.card-item')!;
				const itemSimples = this.obterDadosDoItemSimples(card);
				if (!itemSimples) {
					return;
				}

				this.controladoraRegistroPedido.adicionarItemSimples(itemSimples);
			});
		});

		this.listaClientes.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'cliente' || !radio.checked) {
				return;
			}

			const idCliente = radio.value;
			this.controladoraRegistroPedido.adicionarCliente(idCliente);
		});

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
			} else if (elementoClicado.closest('.remover')) {
				this.controladoraRegistroPedido.removerItem(indice);
			}
		});
	}

	private exibirEtapa(etapa: EtapaPedido) {
		this.menuEtapas.querySelectorAll<HTMLElement>('[data-etapa]').forEach((btn) => {
			btn.classList.toggle('active', btn.dataset.etapa === etapa);
		});

		Object.values(EtapaPedido).forEach((valor) => {
			document.querySelector(`#etapa-${valor}`)?.classList.toggle('d-none', valor !== etapa);
		});

		this.limparInputEAdicionarFoco(etapa === EtapaPedido.ATENDIMENTO ? this.inputFiltroCliente : this.inputFiltroCardapio);
	}

	private exibirMenuCategoriasCardapio(categoria: CategoriaCardapio) {
		this.limparInputEAdicionarFoco(this.inputFiltroCardapio);

		Object.values(CategoriaCardapio).forEach((valor) => {
			document.querySelector(`#secao-${valor}`)?.classList.toggle('d-none', valor !== categoria);
		});
	}

	private atualizarRestricoesTipoAtendimento(): void {
		const tipoConsumidorFinal = this.menuTiposCliente.querySelector<HTMLInputElement>('#tipo-consumidor-final')!;
		const tipoClienteIdentificado = this.menuTiposCliente.querySelector<HTMLInputElement>('#tipo-cliente-identificado')!;
		const tipoEntrega = this.menuTiposAtendimento.querySelector<HTMLInputElement>(`input[name="tipo-atendimento"][value="${TipoAtendimento.ENTREGA}"]`)!;

		tipoConsumidorFinal.disabled = tipoEntrega.checked;
		tipoEntrega.disabled = tipoConsumidorFinal.checked;

		if (tipoEntrega.checked && tipoConsumidorFinal.checked) {
			tipoClienteIdentificado.checked = true;
			tipoConsumidorFinal.checked = false;
			this.atualizarSecaoCliente(TipoCliente.CLIENTE_IDENTIFICADO);
		}
	}

	private atualizarSecaoCliente(tipoCliente: TipoCliente): void {
		const ehConsumidorFinal = tipoCliente === TipoCliente.CONSUMIDOR_FINAL;
		this.secaoCliente.classList.toggle('d-none', ehConsumidorFinal);
		this.secaoConsumidorFinal.classList.toggle('d-none', !ehConsumidorFinal);

		if (tipoCliente === TipoCliente.CONSUMIDOR_FINAL) {
			this.controladoraRegistroPedido.removerCliente();
		} else {
			this.listaClientes.textContent = '';
		}
	}

	private limparInputEAdicionarFoco(input: HTMLInputElement): void {
		input.value = '';
		input.focus();
	}

	obterFiltroCardapio(): FiltroCardapio {
		return {
			categoria: this.obterCategoriaCardapioSelecionada(),
			busca: this.inputFiltroCardapio.value.trim(),
		};
	}

	private obterCategoriaCardapioSelecionada(): CategoriaCardapio {
		const radioSelecionado = this.menuCategoriasCardapio.querySelector<HTMLInputElement>('input[name="categoria-cardapio"]:checked');
		return (radioSelecionado?.value as CategoriaCardapio) ?? CategoriaCardapio.PIZZA;
	}

	private exibirCardsNoCardapio(seletorDiv: string, cards: DocumentFragment[], mensagemVazio: string) {
		const div = document.querySelector<HTMLElement>(seletorDiv)!;
		div.innerHTML = '';

		if (cards.length === 0) {
			div.appendChild(this.criarAvisoCardapioErro(mensagemVazio));
			return;
		}

		const fragmento = document.createDocumentFragment();
		cards.forEach((card) => fragmento.appendChild(card));
		div.appendChild(fragmento);
	}

	private exibirErroNoCardapio(seletorDiv: string, mensagem: string) {
		const div = document.querySelector<HTMLElement>(seletorDiv)!;
		div.innerHTML = '';
		div.appendChild(this.criarAvisoCardapioErro(mensagem));
	}

	private criarAvisoCardapioErro(mensagem: string): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-cardapio-erro')!.content.cloneNode(true) as DocumentFragment;
		template.querySelector<HTMLElement>('.mensagem')!.textContent = mensagem;

		return template;
	}

	exibirPizzasNoCardapio(pizzas: Pizza[]) {
		this.exibirCardsNoCardapio(
			'#secao-pizza > .row',
			pizzas.map((pizza) => this.criarCardPizza(pizza)),
			'Nenhuma pizza encontrada.',
		);
	}

	exibirErroAoObterPizzasParaCardapio(mensagem: string) {
		this.exibirErroNoCardapio('#secao-pizza > .row', mensagem);
	}

	private criarCardPizza(pizza: Pizza): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-card-pizza')!.content.cloneNode(true) as DocumentFragment;

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idPizza = String(pizza.id);
		card.dataset.nome = pizza.nome;

		const img = card.querySelector<HTMLImageElement>('.card-img-top')!;
		img.src = pizza.imagem;
		img.alt = pizza.nome;

		card.querySelector('.card-title')!.textContent = pizza.nome;
		card.querySelector('.card-text')!.textContent = pizza.descricao;

		const grupoTamanhos = card.querySelector('.tamanhos-pizza')!;
		pizza.precosPorTamanho.forEach((precoPorTamanho: PrecoPorTamanho, indice) => {
			grupoTamanhos.appendChild(this.criarOpcaoTamanho(pizza.id, precoPorTamanho, indice === 0));
		});

		return template;
	}

	private criarOpcaoTamanho(idPizza: number, precoPorTamanho: PrecoPorTamanho, selecionado: boolean): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-tamanho-pizza')!.content.cloneNode(true) as DocumentFragment;

		const input = template.querySelector<HTMLInputElement>('input[type="radio"]')!;
		input.id = `tamanho-${idPizza}-${precoPorTamanho.tamanhoPizza}`;
		input.name = `tamanho-pizza-${idPizza}`;
		input.value = precoPorTamanho.tamanhoPizza;
		input.checked = selecionado;
		input.dataset.preco = String(precoPorTamanho.preco);

		const label = template.querySelector<HTMLLabelElement>('label')!;
		label.htmlFor = input.id;
		label.textContent = `${rotuloTamanho(precoPorTamanho.tamanhoPizza)} - R$ ${precoPorTamanho.preco.toFixed(2)}`;

		return template;
	}

	exibirProdutosSimplesNoCardapio(produtos: ProdutoSimples[], tipo: TipoProdutoSimples) {
		const seletorDivCardapioProdutos = `${this.obterSeletorDivCardapioProdutosSimples(tipo)} > .row`;
		const mensagemVazio = tipo === TipoProdutoSimples.BEBIDA ? 'Nenhuma bebida encontrada.' : 'Nenhuma sobremesa encontrada.';

		this.exibirCardsNoCardapio(
			seletorDivCardapioProdutos,
			produtos.map((produto) => this.criarCardProdutoSimples(produto)),
			mensagemVazio,
		);
	}

	exibirErroAoObterProdutosSimplesParaCardapio(mensagem: string, tipo: TipoProdutoSimples) {
		const seletorDivCardapioProdutos = `${this.obterSeletorDivCardapioProdutosSimples(tipo)} > .row`;
		this.exibirErroNoCardapio(seletorDivCardapioProdutos, mensagem);
	}

	private obterSeletorDivCardapioProdutosSimples(tipo: TipoProdutoSimples): string {
		return tipo === TipoProdutoSimples.BEBIDA ? '#secao-bebida' : '#secao-sobremesa';
	}

	private criarCardProdutoSimples(produto: ProdutoSimples): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-card-produto-simples')!.content.cloneNode(true) as DocumentFragment;

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idProduto = String(produto.id);
		card.dataset.nome = produto.nome;
		card.dataset.preco = String(produto.preco);

		const img = card.querySelector<HTMLImageElement>('.card-img-top')!;
		img.src = produto.imagem;
		img.alt = produto.nome;

		card.querySelector('.card-title')!.textContent = produto.nome;

		const precoSpan = card.querySelector<HTMLElement>('.preco')!;
		precoSpan.textContent = `R$ ${produto.preco.toFixed(2)}`;

		return template;
	}

	obterFiltroCliente(): string {
		return this.inputFiltroCliente.value.trim();
	}

	exibirClientes(clientes: Cliente[]) {
		const div = document.querySelector<HTMLElement>('#lista-clientes')!;
		div.innerHTML = '';

		if (clientes.length === 0) {
			div.appendChild(this.criarAvisoCliente('Nenhum cliente encontrado.'));
			return;
		}

		const inputsClientes = clientes.map((cliente) => this.criarInputCliente(cliente));
		const fragmento = document.createDocumentFragment();
		inputsClientes.forEach((input) => fragmento.appendChild(input));

		div.appendChild(fragmento);
	}

	exibirErroAoObterClientes(mensagem: string) {
		const div = document.querySelector<HTMLElement>('#lista-clientes')!;
		div.innerHTML = '';
		div.appendChild(this.criarAvisoCliente(mensagem));
	}

	private criarAvisoCliente(mensagem: string): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-cliente-erro')!.content.cloneNode(true) as DocumentFragment;
		template.querySelector<HTMLElement>('.mensagem')!.textContent = mensagem;

		return template;
	}

	private criarInputCliente(cliente: Cliente): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-cliente')!.content.cloneNode(true) as DocumentFragment;

		const input = template.querySelector<HTMLInputElement>('input[type="radio"]')!;
		input.id = `cliente-${cliente.id}`;
		input.name = 'cliente';
		input.value = String(cliente.id);
		input.dataset.nome = cliente.nome;
		input.dataset.telefone = cliente.telefone;
		input.dataset.cpf = cliente.cpf;

		const label = template.querySelector<HTMLLabelElement>('label')!;
		const span = label.querySelector<HTMLSpanElement>('span')!;
		span.textContent = `(${cliente.cpf}) - ${cliente.nome}`;

		const small = label.querySelector<HTMLSpanElement>('small')!;
		small.textContent = cliente.telefone;

		return template;
	}

	private obterDadosDoItemPizza(card: HTMLElement) {
		const tamanhoSelecionado = card.querySelector<HTMLInputElement>('input[name^="tamanho-pizza-"]:checked');
		if (!tamanhoSelecionado) {
			return;
		}

		return {
			quantidade: 1,
			tamanho: tamanhoSelecionado.value,
			preco: Number(tamanhoSelecionado.dataset.preco),
			saboresEscolhidos: [
				{
					idPizza: Number(card.dataset.idPizza),
					nome: card.dataset.nome ?? '',
				},
			],
		};
	}

	private obterDadosDoItemSimples(card: HTMLElement) {
		return {
			quantidade: 1,
			idProduto: Number(card.dataset.idProduto),
			nome: card.dataset.nome ?? '',
			preco: Number(card.dataset.preco),
		};
	}

	exibirErroNoPedido(mensagem: string): void {
		const template = document.querySelector<HTMLTemplateElement>('#template-pedido-erro')!.content.cloneNode(true) as DocumentFragment;
		const mensagemErro = template.querySelector<HTMLElement>('.mensagem')!;
		mensagemErro.textContent = mensagem;

		this.tituloCarrinho.append(template);

		window.setTimeout(() => {
			this.tituloCarrinho.querySelector('.alert')?.remove();
		}, 5000);
	}

	exibirResumoPedido(pedido: Pedido): void {
		const itensPedido = pedido.itensPedido;
		const avisoCarrinhoVazio = document.querySelector<HTMLElement>('#aviso-vazio')!;

		this.listaItensPedido.innerHTML = '';
		avisoCarrinhoVazio.classList.toggle('d-none', itensPedido.length > 0);

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

	private atualizarValoresResumo(pedido: Pedido): void {
		const subtotal = pedido.itensPedido.reduce((total, item) => {
			return total + (item instanceof ItemPizza || item instanceof ItemSimples ? item.preco * item.quantidade : 0);
		}, 0);

		const divSubtotal = document.querySelector<HTMLElement>('#subtotal')!;
		divSubtotal.textContent = `R$ ${subtotal.toFixed(2)}`;

		const divValorDescontoPromocao = document.querySelector<HTMLElement>('#valor-desconto-promocao')!;
		divValorDescontoPromocao.textContent = `-${pedido.valorDescontoPromocao.toFixed(2)}`;

		const divValorDescontoFidelidade = document.querySelector<HTMLElement>('#valor-desconto-fidelidade')!;
		divValorDescontoFidelidade.textContent = `-${pedido.valorDescontoFidelidade.toFixed(2)}`;

		const divTaxaEntrega = document.querySelector<HTMLElement>('#valor-entrega')!;
		divTaxaEntrega.textContent = `R$ ${pedido.taxaEntrega.toFixed(2)}`;

		const divValorTotal = document.querySelector<HTMLElement>('#valor-total')!;
		divValorTotal.textContent = `R$ ${pedido.valorTotal.toFixed(2)}`;

		const secaoEntrega = document.querySelector<HTMLElement>('#secao-entrega')!;
		secaoEntrega.classList.toggle('d-none', pedido.tipoAtendimento !== TipoAtendimento.ENTREGA);

		const secaoPromocoes = document.querySelector<HTMLElement>('#secao-promocoes')!;
		secaoPromocoes.classList.toggle('d-none', pedido.valorDescontoPromocao <= 0);

		const secaoFidelidade = document.querySelector<HTMLElement>('#secao-fidelidade')!;
		secaoFidelidade.classList.toggle('d-none', pedido.valorDescontoFidelidade <= 0);
	}

	private criarItemPizzaNoResumo(item: ItemPizza, indice: number): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-item-pedido')!.content.cloneNode(true) as DocumentFragment;
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
		divPrecoItem.textContent = `R$ ${item.preco.toFixed(2)}`;

		return template;
	}

	private criarItemSimplesNoResumo(item: ItemSimples, indice: number): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-item-pedido')!.content.cloneNode(true) as DocumentFragment;
		const elemento = template.querySelector<HTMLElement>('.item-pedido')!;
		elemento.dataset.indice = String(indice);

		const divNomeItem = elemento.querySelector<HTMLElement>('.nome-item')!;
		divNomeItem.textContent = item.nome;

		const divSaboresItem = elemento.querySelector<HTMLElement>('.sabores-item')!;
		divSaboresItem.textContent = '';

		const inputQuantidade = elemento.querySelector<HTMLInputElement>('.quantidade-item')!;
		inputQuantidade.value = String(item.quantidade);

		const divPrecoItem = elemento.querySelector<HTMLElement>('.preco-item')!;
		divPrecoItem.textContent = `R$ ${item.preco.toFixed(2)}`;

		return template;
	}
}
