import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { PapelIngrediente } from './papel-ingrediente';

export class IngredienteEscolhido {
	public constructor(
		public readonly ingrediente: Ingrediente,
		public readonly papel: PapelIngrediente,
	) {}
}
