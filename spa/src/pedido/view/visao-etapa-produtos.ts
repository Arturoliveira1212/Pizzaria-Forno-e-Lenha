import { PizzaParaCardapio } from '../../pizza/dominio/pizza-para-cardapio';
import { PrecoPorTamanho } from '../../pizza/dominio/preco-por-tamanho';
import { rotuloTamanho, TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { ProdutoSimples } from '../../produto-simples/dominio/produto-simples';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { formatarDinheiro } from '../../util/dinheiro';
import { clonarTemplate } from '../../util/dom';
import { CategoriaCardapio } from '../categoria-cardapio';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { DadosItemPizza } from '../dados-item-pizza';
import { DadosItemSimples } from '../dados-item-simples';
import { PapelIngrediente } from '../dominio/papel-ingrediente';
import { FiltroCardapio } from '../filtro-cardapio';

export class VisaoEtapaProdutos {
	private controladoraRegistroPedido!: ControladoraRegistroPedido;

	private readonly menuCategoriasCardapio = document.querySelector<HTMLElement>('#categorias-cardapio')!;
	private readonly inputFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLInputElement>('#filtro-cardapio')!;
	private readonly formularioFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLElement>('#formulario-filtro-cardapio')!;
	private readonly secaoPizza = document.querySelector<HTMLElement>('#secao-pizza')!;
	private readonly secoesProdutosSimples = ['#secao-bebida', '#secao-sobremesa'];

	iniciar(controladora: ControladoraRegistroPedido) {
		this.controladoraRegistroPedido = controladora;

		this.registrarEventos();
		this.limparInputFiltroEAdicionarFoco();
		this.controladoraRegistroPedido.obterCardapio();
	}

	private registrarEventos() {
		this.menuCategoriasCardapio.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'categoria-cardapio' || !radio.checked) {
				return;
			}

			const categoria = (radio.value as CategoriaCardapio) ?? CategoriaCardapio.PIZZA;
			this.exibirMenuCategoriasCardapio(categoria);

			this.controladoraRegistroPedido.obterCardapio();
		});

		this.formularioFiltroCardapio.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraRegistroPedido.obterCardapio();
		});

		this.secaoPizza.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLButtonElement>('.adicionar-item');
			if (!botao) {
				return;
			}

			const card = botao.closest<HTMLElement>('.card-item')!;
			const itemPizza = this.obterDadosItemPizza(card);
			if (!itemPizza) {
				return;
			}

			this.controladoraRegistroPedido.adicionarItemPizza(itemPizza);
		});

		this.secoesProdutosSimples.forEach((seletorSecao) => {
			document.querySelector<HTMLElement>(seletorSecao)!.addEventListener('click', (event: Event) => {
				const elementoClicado = event.target as HTMLElement;
				const botao = elementoClicado.closest<HTMLButtonElement>('.adicionar-item');
				if (!botao) {
					return;
				}

				const card = botao.closest<HTMLElement>('.card-item')!;
				const itemSimples = this.obterDadosItemSimples(card);
				if (!itemSimples) {
					return;
				}

				this.controladoraRegistroPedido.adicionarItemSimples(itemSimples);
			});
		});
	}

	private exibirMenuCategoriasCardapio(categoria: CategoriaCardapio) {
		this.limparInputFiltroEAdicionarFoco();

		Object.values(CategoriaCardapio).forEach((valor) => {
			document.querySelector(`#secao-${valor}`)?.classList.toggle('d-none', valor !== categoria);
		});
	}

	limparInputFiltroEAdicionarFoco(): void {
		this.inputFiltroCardapio.value = '';
		this.inputFiltroCardapio.focus();
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

	private criarAvisoCardapioErro(mensagem: string): DocumentFragment {
		const template = clonarTemplate('template-cardapio-erro');
		template.querySelector<HTMLElement>('.mensagem')!.textContent = mensagem;

		return template;
	}

	private exibirCardsNoCardapio(seletorDiv: string, cards: DocumentFragment[], mensagemVazio: string) {
		const div = document.querySelector<HTMLElement>(seletorDiv)!;
		div.replaceChildren();

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
		div.replaceChildren();
		div.appendChild(this.criarAvisoCardapioErro(mensagem));
	}

	exibirPizzasNoCardapio(pizzas: PizzaParaCardapio[]) {
		this.exibirCardsNoCardapio(
			'#secao-pizza > .row',
			pizzas.map((pizza) => this.criarCardPizza(pizza)),
			'Nenhuma pizza encontrada.',
		);
	}

	exibirErroAoObterPizzasParaCardapio(mensagem: string) {
		this.exibirErroNoCardapio('#secao-pizza > .row', mensagem);
	}

	private criarCardPizza(pizza: PizzaParaCardapio): DocumentFragment {
		const template = clonarTemplate('template-card-pizza');

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idPizza = String(pizza.id);
		card.dataset.nome = pizza.nome;
		card.dataset.idsIngredientes = pizza.ingredientes.map((ingrediente) => ingrediente.id).join(',');

		const img = card.querySelector<HTMLImageElement>('.card-img-top')!;
		img.src = pizza.imagem;
		img.alt = pizza.nome;

		card.querySelector('.card-title')!.textContent = pizza.nome;
		card.querySelector('.card-text')!.textContent = pizza.descricao;

		const grupoTamanhos = card.querySelector('.tamanhos-pizza')!;
		pizza.precosPorTamanho.forEach((precoPorTamanho: PrecoPorTamanho, indice: number) => {
			grupoTamanhos.appendChild(this.criarOpcaoTamanho(pizza.id, precoPorTamanho, indice === 0));
		});

		return template;
	}

	private criarOpcaoTamanho(idPizza: number, precoPorTamanho: PrecoPorTamanho, selecionado: boolean): DocumentFragment {
		const template = clonarTemplate('template-tamanho-pizza');

		const input = template.querySelector<HTMLInputElement>('input[type="radio"]')!;
		input.id = `tamanho-${idPizza}-${precoPorTamanho.tamanhoPizza}`;
		input.name = `tamanho-pizza-${idPizza}`;
		input.value = precoPorTamanho.tamanhoPizza;
		input.checked = selecionado;
		input.dataset.preco = String(precoPorTamanho.preco);

		const label = template.querySelector<HTMLLabelElement>('label')!;
		label.htmlFor = input.id;
		label.textContent = `${rotuloTamanho(precoPorTamanho.tamanhoPizza)} - ${formatarDinheiro(precoPorTamanho.preco)}`;

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
		const template = clonarTemplate('template-card-produto-simples');

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idProduto = String(produto.id);
		card.dataset.nome = produto.nome;
		card.dataset.preco = String(produto.preco);

		const img = card.querySelector<HTMLImageElement>('.card-img-top')!;
		img.src = produto.imagem;
		img.alt = produto.nome;

		card.querySelector('.card-title')!.textContent = produto.nome;

		const precoSpan = card.querySelector<HTMLElement>('.preco')!;
		precoSpan.textContent = formatarDinheiro(produto.preco);

		return template;
	}

	private obterDadosItemPizza(card: HTMLElement): DadosItemPizza | undefined {
		const tamanhoSelecionado = card.querySelector<HTMLInputElement>('input[name^="tamanho-pizza-"]:checked');
		if (!tamanhoSelecionado) {
			return;
		}

		const ingredientesEscolhidos = card.dataset.idsIngredientes?.split(',').map((id) => {
			return {
				idIngrediente: Number(id),
				papel: PapelIngrediente.PADRAO,
			};
		});
		if (!ingredientesEscolhidos) {
			return;
		}

		return {
			quantidade: 1,
			tamanho: tamanhoSelecionado.value as TamanhoPizza,
			preco: Number(tamanhoSelecionado.dataset.preco),
			saboresEscolhidos: [
				{
					idPizza: Number(card.dataset.idPizza),
					nome: card.dataset.nome ?? '',
					ingredientesEscolhidos: ingredientesEscolhidos,
				},
			],
		};
	}

	private obterDadosItemSimples(card: HTMLElement): DadosItemSimples {
		return {
			quantidade: 1,
			idProduto: Number(card.dataset.idProduto),
			nome: card.dataset.nome ?? '',
			preco: Number(card.dataset.preco),
		};
	}
}
