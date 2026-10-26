import { Ingrediente } from '../../ingrediente/dominio/ingrediente';

export class IngredienteSubstituto {
	constructor(
		public readonly id: number,
		public readonly ingrediente: Ingrediente,
	) {}
}
