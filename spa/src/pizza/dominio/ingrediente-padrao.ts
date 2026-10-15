import { Ingrediente } from '../../ingrediente/dominio/ingrediente';

export class IngredientePadrao {
	constructor(
		public readonly id: number,
		public readonly ingrediente: Ingrediente,
		public readonly removivel: boolean,
		public readonly substituivel: boolean,
	) {}
}
