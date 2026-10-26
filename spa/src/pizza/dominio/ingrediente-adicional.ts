import { Ingrediente } from '../../ingrediente/dominio/ingrediente';

export class IngredienteAdicional {
	constructor(
		public readonly id: number,
		public readonly ingrediente: Ingrediente,
		public readonly preco: number,
		public readonly quantidade: number,
	) {}
}
