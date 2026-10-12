import { Pizza } from '../../pizza/dominio/pizza';
import { IngredienteEscolhido } from './ingrediente-escolhido';

export class SaborEscolhido {
	public constructor(
		public readonly pizza: Pizza,
		public readonly ingredientesEscolhidos: IngredienteEscolhido[] = [],
	) {}

	chave(): string {
		const ingredientes = this.ingredientesEscolhidos.map((i) => `${i.ingrediente.id}:${i.papel}`).sort();

		return `${this.pizza.id}|${ingredientes.join(',')}`;
	}
}
