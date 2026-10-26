import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { QuantidadePorTamanho } from './quantidade-por-tamanho';

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
}
