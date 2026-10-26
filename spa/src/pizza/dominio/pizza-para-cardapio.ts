import { API } from '../../infra/constantes';
import { IngredientePadrao } from './ingrediente-padrao';
import { PrecoPorTamanho } from './preco-por-tamanho';

export class PizzaParaCardapio {
	public readonly imagem: string;

	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly descricao: string,
		public readonly precosPorTamanho: PrecoPorTamanho[],
		public readonly ingredientesPadrao: IngredientePadrao[],
	) {
		this.imagem = `${API}/assets/pizza/${id}.jpg`;
	}
}
