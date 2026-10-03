import { Pizza } from '../../pizza/dominio/pizza';
import { PrecoPorTamanho } from '../../pizza/dominio/preco-por-tamanho';
import { rotuloTamanho } from '../../pizza/dominio/tamanho-pizza';
import { ProdutoSimples } from '../../produto-simples/dominio/produto-simples';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { CategoriaCardapio } from '../dominio/categoria-cardapio';
import { EtapaPedido } from '../dominio/etapa-pedido';
import { FiltroCardapio } from '../filtro-cardapio';

export class VisaoRegistroPedido {
	private readonly controladoraRegistroPedido: ControladoraRegistroPedido;
	private readonly menuEtapas = document.querySelector<HTMLElement>('#etapas')!;
	private readonly menuCategoriasCardapio = document.querySelector<HTMLElement>('#categorias-cardapio')!;

	constructor() {
		this.controladoraRegistroPedido = new ControladoraRegistroPedido(this);
	}

	iniciar() {
		this.menuEtapas.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLElement>('[data-etapa]');
			if (!botao) {
				return;
			}

			const etapa = (botao?.dataset.etapa as EtapaPedido) ?? EtapaPedido.ATENDIMENTO;
			this.exibirEtapa(etapa);
		});

		this.menuCategoriasCardapio.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLElement>('[data-categoria]');
			if (!botao) {
				return;
			}

			const categoria = (botao?.dataset.categoria as CategoriaCardapio) ?? CategoriaCardapio.PIZZA;
			this.exibirMenuCategoriasCardapio(categoria);

			this.controladoraRegistroPedido.obterCardapio();
		});

		const formularioFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLElement>('#formulario-filtro-cardapio')!;
		formularioFiltroCardapio.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraRegistroPedido.obterCardapio();
		});

		this.controladoraRegistroPedido.obterCardapio();
	}

	private exibirEtapa(etapa: EtapaPedido) {
		this.menuEtapas.querySelectorAll<HTMLElement>('[data-etapa]').forEach((btn) => {
			btn.classList.toggle('active', btn.dataset.etapa === etapa);
		});

		Object.values(EtapaPedido).forEach((valor) => {
			document.querySelector(`#etapa-${valor}`)?.classList.toggle('d-none', valor !== etapa);
		});
	}

	private exibirMenuCategoriasCardapio(categoria: CategoriaCardapio) {
		this.menuCategoriasCardapio.querySelectorAll<HTMLElement>('[data-categoria]').forEach((el) => {
			el.classList.toggle('active', el.dataset.categoria === categoria);
		});

		Object.values(CategoriaCardapio).forEach((valor) => {
			document.querySelector(`#categoria-${valor}`)?.classList.toggle('d-none', valor !== categoria);
		});
	}

	obterFiltroCardapio(): FiltroCardapio {
		return {
			categoria: this.obterCategoriaCardapioSelecionada(),
			busca: this.obterFiltroDigitado(),
		};
	}

	private obterCategoriaCardapioSelecionada(): CategoriaCardapio {
		const botaoSelecionado = this.menuCategoriasCardapio.querySelector<HTMLElement>('.active');
		return (botaoSelecionado?.dataset.categoria as CategoriaCardapio) ?? CategoriaCardapio.PIZZA;
	}

	private obterFiltroDigitado(): string {
		const inputFiltro = this.menuCategoriasCardapio.querySelector<HTMLInputElement>('#filtro-cardapio');
		return inputFiltro?.value ?? '';
	}

	private exibirCardsNoCardapio(seletorDiv: string, cards: DocumentFragment[], mensagemVazio: string) {
		const container = document.querySelector<HTMLElement>(seletorDiv)!;
		container.innerHTML = '';

		if (cards.length === 0) {
			container.appendChild(this.criarAvisoCardapioErro(mensagemVazio));
			return;
		}

		const fragmento = document.createDocumentFragment();
		cards.forEach((card) => fragmento.appendChild(card));
		container.appendChild(fragmento);
	}

	private exibirErroNoCardapio(seletorDiv: string, mensagem: string) {
		const container = document.querySelector<HTMLElement>(seletorDiv)!;
		container.innerHTML = '';
		container.appendChild(this.criarAvisoCardapioErro(mensagem));
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
		const rotulo = rotuloTamanho(precoPorTamanho.tamanhoPizza);

		const input = template.querySelector<HTMLInputElement>('input[type="radio"]')!;
		input.id = `tamamnho-${idPizza}-${precoPorTamanho.tamanhoPizza}`;
		input.name = `tamanho-pizza-${idPizza}`;
		input.dataset.tamanhoPizza = precoPorTamanho.tamanhoPizza;
		input.value = rotulo;
		input.checked = selecionado;

		const label = template.querySelector<HTMLLabelElement>('label')!;
		label.htmlFor = input.id;
		label.textContent = `${rotulo} - R$ ${precoPorTamanho.preco.toFixed(2)}`;

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
}
