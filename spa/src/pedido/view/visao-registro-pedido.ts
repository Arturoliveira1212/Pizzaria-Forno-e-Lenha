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
			document.querySelector(`#categoria-${valor}`)?.classList.toggle('d-none', valor !== categoria);
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
			'#categoria-pizza > .row',
			pizzas.map((pizza) => this.criarCardPizza(pizza)),
			'Nenhuma pizza encontrada.',
		);
	}

	exibirErroAoObterPizzasParaCardapio(mensagem: string) {
		this.exibirErroNoCardapio('#categoria-pizza > .row', mensagem);
	}

	private criarCardPizza(pizza: Pizza): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-card-pizza')!.content.cloneNode(true) as DocumentFragment;

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idPizza = String(pizza.id);

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
		input.id = `tamamnho-${idPizza}-${precoPorTamanho.tamanhoPizza}`;
		input.name = `tamanho-pizza-${idPizza}`;
		input.value = precoPorTamanho.tamanhoPizza;
		input.checked = selecionado;

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
		return tipo === TipoProdutoSimples.BEBIDA ? '#categoria-bebida' : '#categoria-sobremesa';
	}

	private criarCardProdutoSimples(produto: ProdutoSimples): DocumentFragment {
		const template = document.querySelector<HTMLTemplateElement>('#template-card-produto-simples')!.content.cloneNode(true) as DocumentFragment;

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idProduto = String(produto.id);

		const img = card.querySelector<HTMLImageElement>('.card-img-top')!;
		img.src = produto.imagem;
		img.alt = produto.nome;

		card.querySelector('.card-title')!.textContent = produto.nome;

		const precoSpan = card.querySelector<HTMLElement>('.fw-semibold')!;
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

		const label = template.querySelector<HTMLLabelElement>('label')!;
		const span = label.querySelector<HTMLSpanElement>('span')!;
		span.textContent = `(${cliente.cpf}) - ${cliente.nome}`;

		const small = label.querySelector<HTMLSpanElement>('small')!;
		small.textContent = cliente.telefone;

		return template;
	}
}
