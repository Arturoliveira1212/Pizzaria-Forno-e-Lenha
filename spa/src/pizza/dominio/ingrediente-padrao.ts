import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { QuantidadePorTamanho } from './quantidade-por-tamanho';
import { TamanhoPizza } from './tamanho-pizza';

export class IngredientePadrao {
	constructor(
		public readonly id: number,
		public readonly ingrediente: Ingrediente,
		public readonly removivel: boolean,
		public readonly substituivel: boolean,
		public readonly quantidadesPorTamanho: QuantidadePorTamanho[],
	) {}

	ehFixo(): boolean {
		return !this.removivel && !this.substituivel;
	}

	temEstoqueSuficiente(tamanho: TamanhoPizza): boolean {
		const quantidadePorTamanho = this.quantidadesPorTamanho.find((quantidade) => quantidade.tamanhoPizza === tamanho);
		if (!quantidadePorTamanho) {
			return false;
		}

		return this.ingrediente.temEstoqueSuficiente(quantidadePorTamanho.quantidade);
	}
}
