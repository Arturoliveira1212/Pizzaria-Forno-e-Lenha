import { API } from '../../infra/constantes';
import { PrecoPorTamanho } from './preco-por-tamanho';

export class Pizza {
	public readonly imagem: string;

	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly descricao: string,
		public readonly precosPorTamanho: PrecoPorTamanho[],
	) {
		this.imagem = `${API}/produto.webp`;
	}
}
