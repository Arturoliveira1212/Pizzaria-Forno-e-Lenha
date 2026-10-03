import { API } from '../../infra/constantes';
import { TipoProdutoSimples } from './tipo-produto-simples';

export class ProdutoSimples {
	public readonly imagem: string;

	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly descricao: string,
		public readonly tipo: TipoProdutoSimples,
		public readonly preco: number,
	) {
		this.imagem = `${API}/produto.webp`;
	}
}
