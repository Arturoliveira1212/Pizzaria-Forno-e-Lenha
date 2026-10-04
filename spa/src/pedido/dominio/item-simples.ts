import { ItemPedido } from './item-pedido';

export class ItemSimples extends ItemPedido {
	public constructor(
		quantidade: number,
		public readonly idProduto: number,
		public readonly nome: string,
		preco: number,
	) {
		super(quantidade, preco);
	}

	criarItemSimplesComNovaQuantidade(quantidade: number): ItemSimples {
		return new ItemSimples(quantidade, this.idProduto, this.nome, this.preco);
	}
}
