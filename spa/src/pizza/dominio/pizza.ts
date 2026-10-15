import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { IngredienteAdicional } from './ingrediente-adicional';
import { IngredientePadrao } from './ingrediente-padrao';
import { PrecoPorTamanho } from './preco-por-tamanho';

export class Pizza {
	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly descricao: string = '',
		public readonly ingredientesPadrao: IngredientePadrao[] = [],
		public readonly ingredientesSubstitutos: Ingrediente[] = [],
		public readonly adicionais: IngredienteAdicional[] = [],
		public readonly precosPorTamanho: PrecoPorTamanho[] = [],
	) {}
}
