import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Pizza } from '../../pizza/dominio/pizza';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { IngredientePadrao } from '../../pizza/dominio/ingrediente-padrao';
import { clonarTemplate } from '../../util/dom';
import { IngredienteAdicional } from '../../pizza/dominio/ingrediente-adicional';
import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { formatarDinheiro } from '../../util/dinheiro';
import { rotuloTamanho, TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { DadosItemPizza } from '../dados-item-pizza';

export class VisaoPersonalizacaoPizza {
	private controladoraRegistroPedido!: ControladoraRegistroPedido;

	private readonly modal = bootstrap.Modal.getOrCreateInstance('#modal-personalizacao-pizza')! as bootstrap.Modal;
	private readonly nomePizza = document.querySelector<HTMLElement>('#nome-pizza')!;
	private readonly informacaoTamanho = document.querySelector<HTMLElement>('#informacao-tamanho')!;
	private readonly inputTamanho = this.informacaoTamanho.querySelector<HTMLInputElement>('#tamanho')!;
	private readonly labelTamanho = this.informacaoTamanho.querySelector<HTMLLabelElement>('label')!;
	private readonly inputTipoInteira = document.querySelector<HTMLInputElement>('#tipo-inteira')!;
	private readonly inputTipoMetade = document.querySelector<HTMLInputElement>('#tipo-meia')!;
	private readonly valorTotalPersonalizacao = document.querySelector<HTMLElement>('#valor-total-personalizacao')!;
	private readonly botaoAdicionarPersonalizacaoPizza = document.querySelector<HTMLButtonElement>('#botao-adicionar-personalizacao-pizza')!;

	private readonly listaIngredientesPrimeiroSabor = document.querySelector<HTMLElement>('#lista-ingredientes-primeiro-sabor')!;
	private readonly tituloAdicionaisPrimeiroSabor = document.querySelector<HTMLElement>('#titulo-adicionais-primeiro-sabor')!;
	private readonly listaAdicionaisPrimeiroSabor = document.querySelector<HTMLElement>('#lista-adicionais-primeiro-sabor')!;
	private readonly secaoPizzaPrimeiroSabor = document.querySelector<HTMLElement>('#secao-pizza-primeiro-sabor')!;
	private readonly secaoPizzaSegundoSabor = document.querySelector<HTMLElement>('#secao-pizza-segundo-sabor')!;

	private readonly formularioFiltroSegundoSabor = this.secaoPizzaSegundoSabor.querySelector<HTMLElement>('#formulario-filtro-segundo-sabor')!;
	private readonly inputFiltroSegundoSabor = this.secaoPizzaSegundoSabor.querySelector<HTMLInputElement>('#filtro-segundo-sabor')!;

	iniciar(controladora: ControladoraRegistroPedido): void {
		this.controladoraRegistroPedido = controladora;

		this.registrarEventos();
	}

	registrarEventos(): void {
		this.listaIngredientesPrimeiroSabor.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLInputElement;
			const ingredientePersonalizavel = elementoClicado.closest<HTMLElement>('.ingrediente-personalizavel');
			if (!ingredientePersonalizavel) {
				return;
			}

			if (elementoClicado.closest('.remover-ingrediente')) {
				const marcado = elementoClicado.checked;
				this.removerIngrediente(ingredientePersonalizavel, marcado);
			}
		});

		this.listaAdicionaisPrimeiroSabor.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLInputElement;
			const ingredienteAdicional = elementoClicado.closest<HTMLElement>('.ingrediente-adicional');
			if (!ingredienteAdicional) {
				return;
			}

			if (elementoClicado.closest('.aumentar-quantidade-adicional')) {
				this.atualizarQuantidadeAdicional(ingredienteAdicional, 1);
			} else if (elementoClicado.closest('.diminuir-quantidade-adicional')) {
				this.atualizarQuantidadeAdicional(ingredienteAdicional, -1);
			}
		});

		this.botaoAdicionarPersonalizacaoPizza.addEventListener('click', async () => {
			this.controladoraRegistroPedido.adicionarPersonalizacaoItemPizza();
		});
	}

	exibirPizzaParaPersonalizacao(pizza: Pizza, tamanho: TamanhoPizza): void {
		this.resetarModal();
		this.povoarModal(pizza, tamanho);
		this.modal.show();
	}

	abrirModal(): void {
		this.modal.show();
	}

	fecharModal(): void {
		this.modal.hide();
	}

	private resetarModal(): void {
		this.labelTamanho.textContent = '';
		this.inputTipoInteira.checked = true;
		this.inputTipoMetade.checked = false;
		this.listaIngredientesPrimeiroSabor.replaceChildren();
		this.tituloAdicionaisPrimeiroSabor.classList.add('d-none');
		this.listaAdicionaisPrimeiroSabor.replaceChildren();
		this.secaoPizzaPrimeiroSabor.classList.remove('d-none');
		this.secaoPizzaSegundoSabor.classList.add('d-none');
		this.inputFiltroSegundoSabor.value = '';
	}

	private povoarModal(pizza: Pizza, tamanho: TamanhoPizza): void {
		const preco = pizza.obterPrecoPorTamanho(tamanho);
		const precoFormatado = formatarDinheiro(preco);

		this.nomePizza.textContent = pizza.nome;
		this.informacaoTamanho.dataset.preco = String(preco);
		this.inputTamanho.value = tamanho;
		this.labelTamanho.textContent = `${rotuloTamanho(tamanho)} - ${precoFormatado}`;
		this.valorTotalPersonalizacao.dataset.preco = String(preco);
		this.valorTotalPersonalizacao.textContent = precoFormatado;

		const ingredientesPadrao = pizza.ingredientesPadrao;
		if (ingredientesPadrao.length === 0) {
			return;
		}

		this.listaIngredientesPrimeiroSabor.append(...this.criarListaIngredientes(pizza));

		const adicionais = pizza.adicionais;
		if (adicionais.length > 0) {
			this.tituloAdicionaisPrimeiroSabor.classList.remove('d-none');
			this.listaAdicionaisPrimeiroSabor.append(...this.criarListaAdicionais(adicionais));
		}
	}

	private criarListaIngredientes(pizza: Pizza): DocumentFragment[] {
		const ingredientesPadrao = pizza.ingredientesPadrao;
		const ingredientesSubstitutos = pizza.ingredientesSubstitutos;

		return ingredientesPadrao.map((ingredientePadrao) => {
			if (ingredientePadrao.ehFixo()) {
				return this.criarItemListaIngredienteFixo(ingredientePadrao);
			}

			return this.criarItemListaIngredientePersonalizavel(ingredientePadrao, ingredientesSubstitutos);
		});
	}

	private criarItemListaIngredienteFixo(ingredientePadrao: IngredientePadrao): DocumentFragment {
		const template = clonarTemplate('template-ingrediente-fixo');

		const li = template.querySelector<HTMLLIElement>('.ingrediente-fixo')!;
		li.dataset.idIngrediente = String(ingredientePadrao.ingrediente.id);

		const spanNomeIngrediente = template.querySelector<HTMLSpanElement>('.nome-ingrediente')!;
		spanNomeIngrediente.textContent = ingredientePadrao.ingrediente.nome;

		return template;
	}

	private criarItemListaIngredientePersonalizavel(ingredientePadrao: IngredientePadrao, ingredientesSubstitutos: Ingrediente[]): DocumentFragment {
		const template = clonarTemplate('template-ingrediente-personalizavel');

		const li = template.querySelector<HTMLLIElement>('.ingrediente-personalizavel')!;
		li.dataset.idIngredienteOriginal = String(ingredientePadrao.ingrediente.id);

		const nomeIngredienteOriginal = template.querySelector<HTMLSpanElement>('.nome-ingrediente-original')!;
		nomeIngredienteOriginal.textContent = ingredientePadrao.ingrediente.nome;

		const selectOpcoesTroca = template.querySelector<HTMLSelectElement>('.opcoes-troca')!;
		selectOpcoesTroca.disabled = !ingredientePadrao.substituivel;
		const optionPadrao = this.criarOptionIngrediente(ingredientePadrao.ingrediente);

		let options = [] as HTMLOptionElement[];
		if (ingredientePadrao.substituivel) {
			options = ingredientesSubstitutos.map((substituto) => this.criarOptionIngrediente(substituto));
		}

		selectOpcoesTroca.append(optionPadrao, ...options);

		const inputRemoverIngrediente = template.querySelector<HTMLInputElement>('.remover-ingrediente')!;
		inputRemoverIngrediente.disabled = !ingredientePadrao.removivel;

		return template;
	}

	private criarOptionIngrediente(ingrediente: Ingrediente): HTMLOptionElement {
		const option = document.createElement('option');
		option.value = String(ingrediente.id);
		option.textContent = ingrediente.nome;

		return option;
	}

	private criarListaAdicionais(adicionais: IngredienteAdicional[]): DocumentFragment[] {
		return adicionais.map((adicional) => this.criarItemListaAdicional(adicional));
	}

	private criarItemListaAdicional(adicional: IngredienteAdicional): DocumentFragment {
		const template = clonarTemplate('template-adicional');

		const li = template.querySelector<HTMLLIElement>('.ingrediente-adicional')!;
		li.dataset.preco = String(adicional.preco);

		const spanNomeAdicional = template.querySelector<HTMLSpanElement>('.nome-adicional')!;
		spanNomeAdicional.textContent = adicional.ingrediente.nome;

		const precoAdicional = template.querySelector<HTMLSpanElement>('.preco-adicional')!;
		precoAdicional.textContent = formatarDinheiro(adicional.preco);

		const inputQuantidadeAdicional = template.querySelector<HTMLInputElement>('.quantidade-adicional')!;
		inputQuantidadeAdicional.value = '0';

		return template;
	}

	private removerIngrediente(ingredientePersonalizavel: HTMLElement, marcado: boolean): void {
		ingredientePersonalizavel.classList.toggle('list-group-item-danger', marcado);

		const selectOpcoesTroca = ingredientePersonalizavel.querySelector<HTMLSelectElement>('.opcoes-troca')!;
		selectOpcoesTroca.classList.toggle('text-decoration-line-through', marcado);
		selectOpcoesTroca.classList.toggle('bg-transparent', marcado);

		const nomeIngredienteOriginal = ingredientePersonalizavel.querySelector<HTMLSpanElement>('.nome-ingrediente-original')!;
		nomeIngredienteOriginal.classList.toggle('text-decoration-line-through', marcado);
	}

	private atualizarQuantidadeAdicional(ingredienteAdicional: HTMLElement, delta: number): void {
		const inputQuantidade = ingredienteAdicional.querySelector<HTMLInputElement>('.quantidade-adicional')!;
		const quantidadeAtual = Number(inputQuantidade.value);
		const novaQuantidade = Math.max(0, quantidadeAtual + delta);

		inputQuantidade.value = String(novaQuantidade);

		if (novaQuantidade !== quantidadeAtual) {
			this.atualizarValorTotalPersonalizacao();
		}
	}

	private atualizarValorTotalPersonalizacao(): void {
		let valorTotal = Number(this.informacaoTamanho.dataset.preco);

		const ingredientesAdicionais = this.listaAdicionaisPrimeiroSabor.querySelectorAll<HTMLElement>('.ingrediente-adicional');
		ingredientesAdicionais.forEach((ingredienteAdicional) => {
			const precoAdicional = Number(ingredienteAdicional.dataset.preco) || 0;
			const quantidade = Number(ingredienteAdicional.querySelector<HTMLInputElement>('.quantidade-adicional')?.value || 0);

			valorTotal += precoAdicional * quantidade;
		});

		this.valorTotalPersonalizacao.dataset.preco = String(valorTotal);
		this.valorTotalPersonalizacao.textContent = formatarDinheiro(valorTotal);
	}
}
