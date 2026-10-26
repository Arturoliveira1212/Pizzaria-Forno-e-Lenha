import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { QuantidadePorTamanho } from './quantidade-por-tamanho';

export class IngredienteSubstituto {
	constructor(
		public readonly id: number,
		public readonly ingrediente: Ingrediente,
		public readonly quantidadesPorTamanho: QuantidadePorTamanho[],
	) {}
}
