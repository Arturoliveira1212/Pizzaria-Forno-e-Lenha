import { TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { ItemPedido } from './item-pedido';
import { SaborEscolhido } from './sabor-escolhido';

export class ItemPizza extends ItemPedido {
	public constructor(
		quantidade: number,
		public readonly saboresEscolhidos: SaborEscolhido[],
		public readonly tamanhoPizza: TamanhoPizza,
		public readonly preco: number,
	) {
		super(quantidade);
	}

	criarItemPizzaComNovaQuantidade(quantidade: number): ItemPizza {
		return new ItemPizza(quantidade, this.saboresEscolhidos, this.tamanhoPizza, this.preco);
	}
}
