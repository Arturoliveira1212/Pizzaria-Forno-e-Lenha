import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { Pizza } from '../../pizza/dominio/pizza';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { IngredientePadrao } from '../../pizza/dominio/ingrediente-padrao';
import { clonarTemplate } from '../../util/dom';
import { IngredienteAdicional } from '../../pizza/dominio/ingrediente-adicional';
import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { formatarDinheiro } from '../../util/dinheiro';
import { rotuloTamanho, TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';

export class VisaoPersonalizacaoPizza {
	private controladoraRegistroPedido!: ControladoraRegistroPedido;

	private readonly modal = bootstrap.Modal.getOrCreateInstance('#modal-personalizacao-pizza')! as bootstrap.Modal;
	private readonly nomePizza = document.querySelector<HTMLElement>('#nome-pizza')!;
	private readonly informacaoTamanho = document.querySelector<HTMLElement>('#informacao-tamanho')!;
	private readonly labelTamanho = this.informacaoTamanho.querySelector<HTMLLabelElement>('label')!;
	private readonly inputTipoInteira = document.querySelector<HTMLInputElement>('#tipo-inteira')!;
	private readonly inputTipoMetade = document.querySelector<HTMLInputElement>('#tipo-meia')!;
	private readonly listaIngredientes = document.querySelector<HTMLElement>('#lista-ingredientes')!;
	private readonly tituloAdicionais = document.querySelector<HTMLElement>('#titulo-adicionais')!;
	private readonly listaAdicionais = document.querySelector<HTMLElement>('#lista-adicionais')!;
	private readonly secaoPizza1 = document.querySelector<HTMLElement>('#secao-pizza1')!;
	private readonly secaoPizza2 = document.querySelector<HTMLElement>('#secao-pizza2')!;
	private readonly formularioFiltroSegundaMetade = this.secaoPizza2.querySelector<HTMLElement>('#formulario-filtro-segunda-metade')!;
	private readonly inputFiltroCliente = this.secaoPizza2.querySelector<HTMLInputElement>('#filtro-segunda-metade')!;

	iniciar(controladora: ControladoraRegistroPedido): void {
		this.controladoraRegistroPedido = controladora;

		this.registrarEventos();
	}

	registrarEventos(): void {}

	exibirPizzaParaPersonalizacao(pizza: Pizza, tamanho: TamanhoPizza): void {
		this.resetarModal();
		this.povoarModal(pizza, tamanho);
		this.modal.show();
	}

	private resetarModal(): void {
		this.labelTamanho.textContent = '';
		this.inputTipoInteira.checked = true;
		this.inputTipoMetade.checked = false;
		this.listaIngredientes.replaceChildren();
		this.tituloAdicionais.classList.add('d-none');
		this.listaAdicionais.replaceChildren();
		this.secaoPizza1.classList.remove('d-none');
		this.secaoPizza2.classList.add('d-none');
		this.inputFiltroCliente.value = '';
	}

	private povoarModal(pizza: Pizza, tamanho: TamanhoPizza): void {
		this.nomePizza.textContent = pizza.nome;
		this.labelTamanho.textContent = `${rotuloTamanho(tamanho)} - ${formatarDinheiro(pizza.obterPrecoPorTamanho(tamanho))}`;

		const ingredientesPadrao = pizza.ingredientesPadrao;
		if (ingredientesPadrao.length === 0) {
			return;
		}

		this.listaIngredientes.append(...this.criarListaIngredientes(pizza));

		const adicionais = pizza.adicionais;
		if (adicionais.length > 0) {
			this.tituloAdicionais.classList.remove('d-none');
			this.listaAdicionais.append(...this.criarListaAdicionais(adicionais));
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
		const spanNomeIngrediente = template.querySelector<HTMLSpanElement>('.nome-ingrediente')!;
		spanNomeIngrediente.textContent = ingredientePadrao.ingrediente.nome;

		return template;
	}

	private criarItemListaIngredientePersonalizavel(ingredientePadrao: IngredientePadrao, ingredientesSubstitutos: Ingrediente[]): DocumentFragment {
		const template = clonarTemplate('template-ingrediente-personalizavel');

		const nomeIngredienteOriginal = template.querySelector<HTMLSpanElement>('.nome-ingrediente-original')!;
		nomeIngredienteOriginal.textContent = ingredientePadrao.ingrediente.nome;

		const selectOpcoesTroca = template.querySelector<HTMLSelectElement>('.opcoes-troca')!;
		const optionPadrao = this.criarOptionIngrediente(ingredientePadrao.ingrediente);
		const options = ingredientesSubstitutos.map((substituto) => this.criarOptionIngrediente(substituto));
		selectOpcoesTroca.append(optionPadrao, ...options);

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

		const spanNomeAdicional = template.querySelector<HTMLSpanElement>('.nome-adicional')!;
		spanNomeAdicional.textContent = adicional.ingrediente.nome;

		const precoAdicional = template.querySelector<HTMLSpanElement>('.preco-adicional')!;
		precoAdicional.textContent = formatarDinheiro(adicional.preco);

		const inputQuantidadeAdicional = template.querySelector<HTMLInputElement>('.quantidade-adicional')!;
		inputQuantidadeAdicional.value = '0';

		return template;
	}
}
