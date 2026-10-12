import { API } from '../../infra/constantes';
import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { PrecoPorTamanho } from './preco-por-tamanho';

export class PizzaParaCardapio {
	public readonly imagem: string;

	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly descricao: string,
		public readonly precosPorTamanho: PrecoPorTamanho[],
		public readonly ingredientes: Ingrediente[],
	) {
		this.imagem = `${API}/assets/pizza/${id}.jpg`;
	}
}
