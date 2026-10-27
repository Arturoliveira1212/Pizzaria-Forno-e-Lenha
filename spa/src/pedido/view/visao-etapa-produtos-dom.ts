import { CategoriaCardapio, DadosItemPizza, DadosItemSimples, FiltroCardapio } from './visao-registro-pedido';
import { PizzaParaCardapio } from '../../pizza/dominio/pizza-para-cardapio';
import { PrecoPorTamanho } from '../../pizza/dominio/preco-por-tamanho';
import { rotuloTamanho, TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { ProdutoSimples } from '../../produto-simples/dominio/produto-simples';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { formatarDinheiro } from '../../util/dinheiro';
import { clonarTemplate, limparInputEAdicionarFoco } from '../../util/dom';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { PapelIngrediente } from '../dominio/papel-ingrediente';
import { VisaoEtapaProdutos } from './visao-etapa-produtos';

export class VisaoEtapaProdutosDom implements VisaoEtapaProdutos {
	private controladoraRegistroPedido!: ControladoraRegistroPedido;

	private readonly menuCategoriasCardapio = document.querySelector<HTMLElement>('#categorias-cardapio')!;
	private readonly inputFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLInputElement>('#filtro-cardapio')!;
	private readonly formularioFiltroCardapio = this.menuCategoriasCardapio.querySelector<HTMLElement>('#formulario-filtro-cardapio')!;
	private readonly secaoPizza = document.querySelector<HTMLElement>('#secao-pizza')!;
	private readonly secoesProdutosSimples = ['#secao-bebida', '#secao-sobremesa'];

	iniciar(controladora: ControladoraRegistroPedido): void {
		this.controladoraRegistroPedido = controladora;

		this.registrarEventos();
		this.limparInputFiltroEAdicionarFoco();
		this.controladoraRegistroPedido.obterCardapio();
	}

	private registrarEventos(): void {
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

			const botaoAdicionarItem = elementoClicado.closest<HTMLButtonElement>('.adicionar-item');
			const botaoPersonalizarItem = elementoClicado.closest<HTMLButtonElement>('.personalizar-item');
			if (!botaoAdicionarItem && !botaoPersonalizarItem) {
				return;
			}

			const botao = botaoAdicionarItem ?? botaoPersonalizarItem!;
			const card = botao.closest<HTMLElement>('.card-item')!;

			if (botaoPersonalizarItem) {
				const idPizza = Number(card.dataset.idPizza);
				if (isNaN(idPizza)) {
					return;
				}

				const tamanhoSelecionado = this.obterDadosTamanhoSelecionado(card);
				if (!tamanhoSelecionado) {
					return;
				}

				this.controladoraRegistroPedido.personalizarItemPizza(idPizza, tamanhoSelecionado.tamanho);
			} else if (botaoAdicionarItem) {
				const itemPizza = this.obterDadosItemPizza(card);
				if (!itemPizza) {
					return;
				}

				this.controladoraRegistroPedido.adicionarItemPizza(itemPizza);
			}
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

	private exibirMenuCategoriasCardapio(categoria: CategoriaCardapio): void {
		this.limparInputFiltroEAdicionarFoco();

		Object.values(CategoriaCardapio).forEach((valor) => {
			document.querySelector(`#secao-${valor}`)?.classList.toggle('d-none', valor !== categoria);
		});
	}

	limparInputFiltroEAdicionarFoco(): void {
		limparInputEAdicionarFoco(this.inputFiltroCardapio);
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

	private exibirCardsNoCardapio(seletorDiv: string, cards: DocumentFragment[], mensagemVazio: string): void {
		const div = document.querySelector<HTMLElement>(seletorDiv)!;
		div.replaceChildren();

		if (cards.length === 0) {
			div.appendChild(this.criarAvisoCardapioErro(mensagemVazio));
			return;
		}

		div.append(...cards);
	}

	private exibirErroNoCardapio(seletorDiv: string, mensagem: string): void {
		const div = document.querySelector<HTMLElement>(seletorDiv)!;
		div.replaceChildren(this.criarAvisoCardapioErro(mensagem));
	}

	exibirPizzasNoCardapio(pizzas: PizzaParaCardapio[]): void {
		this.exibirCardsNoCardapio(
			'#secao-pizza > .row',
			pizzas.map((pizza) => this.criarCardPizza(pizza)),
			'Nenhuma pizza encontrada.',
		);
	}

	exibirErroAoObterPizzasParaCardapio(mensagem: string): void {
		this.exibirErroNoCardapio('#secao-pizza > .row', mensagem);
	}

	private criarCardPizza(pizza: PizzaParaCardapio): DocumentFragment {
		const template = clonarTemplate('template-card-pizza');

		const card = template.querySelector<HTMLElement>('.card-item')!;
		card.dataset.idPizza = String(pizza.id);
		card.dataset.nome = pizza.nome;
		card.dataset.idsIngredientes = pizza.ingredientesPadrao.map((ingredientePadrao) => ingredientePadrao.ingrediente.id).join(',');

		const img = card.querySelector<HTMLImageElement>('.card-img-top')!;
		img.src = pizza.imagem;
		img.alt = pizza.nome;

		card.querySelector('.card-title')!.textContent = pizza.nome;
		card.querySelector('.card-text')!.textContent = pizza.descricao;

		const opcoesTamanho = this.criarOpcoesTamanho(pizza);
		if (opcoesTamanho.length === 0) {
			const botaoAdicionar = card.querySelector<HTMLButtonElement>('.adicionar-item')!;
			const botaoPersonalizar = card.querySelector<HTMLButtonElement>('.personalizar-item')!;
			botaoAdicionar.remove();
			botaoPersonalizar.remove();

			this.adicionarAvisoProdutoSemEstoque(card);
		}

		const grupoTamanhos = card.querySelector('.tamanhos-pizza')!;
		grupoTamanhos.append(...opcoesTamanho);

		return template;
	}

	private adicionarAvisoProdutoSemEstoque(card: HTMLElement): void {
		const templateAvisoSemEstoque = clonarTemplate('template-produto-sem-estoque');
		const cardBody = card.querySelector('.card-body')!;
		cardBody.appendChild(templateAvisoSemEstoque);
	}

	private criarOpcoesTamanho(pizza: PizzaParaCardapio): DocumentFragment[] {
		const opcoesTamanho: DocumentFragment[] = [];

		pizza.precosPorTamanho.forEach((precoPorTamanho) => {
			const tamanho = precoPorTamanho.tamanhoPizza;
			const todosOsIngredientesPossuemEstoqueSuficiente = pizza.ingredientesPadrao.every((ingredientePadrao) => ingredientePadrao.temEstoqueSuficiente(tamanho));
			if (!todosOsIngredientesPossuemEstoqueSuficiente) {
				return;
			}

			opcoesTamanho.push(this.criarOpcaoTamanho(pizza.id, precoPorTamanho, opcoesTamanho.length === 0));
		});

		return opcoesTamanho;
	}

	private criarOpcaoTamanho(idPizza: number, precoPorTamanho: PrecoPorTamanho, selecionado: boolean): DocumentFragment {
		const template = clonarTemplate('template-tamanho-pizza');
		const rotulo = rotuloTamanho(precoPorTamanho.tamanhoPizza);

		const input = template.querySelector<HTMLInputElement>('input[type="radio"]')!;
		input.id = `tamanho-${idPizza}-${precoPorTamanho.tamanhoPizza}`;
		input.name = `tamanho-pizza-${idPizza}`;
		input.value = precoPorTamanho.tamanhoPizza;
		input.checked = selecionado;
		input.dataset.preco = String(precoPorTamanho.preco);

		const label = template.querySelector<HTMLLabelElement>('label')!;
		label.htmlFor = input.id;
		label.textContent = `${rotulo} - ${formatarDinheiro(precoPorTamanho.preco)}`;
		label.dataset.testid = `tamanho-pizza-${rotulo}`;

		return template;
	}

	exibirProdutosSimplesNoCardapio(produtos: ProdutoSimples[], tipo: TipoProdutoSimples): void {
		const seletorDivCardapioProdutos = `${this.obterSeletorDivCardapioProdutosSimples(tipo)} > .row`;
		const mensagemVazio = tipo === TipoProdutoSimples.BEBIDA ? 'Nenhuma bebida encontrada.' : 'Nenhuma sobremesa encontrada.';

		this.exibirCardsNoCardapio(
			seletorDivCardapioProdutos,
			produtos.map((produto) => this.criarCardProdutoSimples(produto)),
			mensagemVazio,
		);
	}

	exibirErroAoObterProdutosSimplesParaCardapio(mensagem: string, tipo: TipoProdutoSimples): void {
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

		if (produto.estoque === 0) {
			const botaoAdicionar = card.querySelector<HTMLButtonElement>('.adicionar-item')!;
			botaoAdicionar.remove();
			precoSpan.remove();

			this.adicionarAvisoProdutoSemEstoque(card);
		}

		return template;
	}

	private obterDadosItemPizza(card: HTMLElement): DadosItemPizza | undefined {
		const tamanhoSelecionado = this.obterDadosTamanhoSelecionado(card);
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
			tamanho: tamanhoSelecionado.tamanho as TamanhoPizza,
			preco: tamanhoSelecionado.preco,
			saboresEscolhidos: [
				{
					idPizza: Number(card.dataset.idPizza),
					nome: card.dataset.nome ?? '',
					ingredientesEscolhidos: ingredientesEscolhidos,
				},
			],
		};
	}

	private obterDadosTamanhoSelecionado(card: HTMLElement): { tamanho: TamanhoPizza; preco: number } | undefined {
		const tamanhoSelecionado = card.querySelector<HTMLInputElement>('input[name^="tamanho-pizza-"]:checked');
		if (!tamanhoSelecionado) {
			return;
		}

		return {
			tamanho: tamanhoSelecionado.value as TamanhoPizza,
			preco: Number(tamanhoSelecionado.dataset.preco),
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
