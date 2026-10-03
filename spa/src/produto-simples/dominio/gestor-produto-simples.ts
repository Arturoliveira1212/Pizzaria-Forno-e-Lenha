import { RepositorioProdutoSimples } from '../repositorio/repositorio-produto-simples';
import { ProdutoSimples } from './produto-simples';
import { TipoProdutoSimples } from './tipo-produto-simples';

export class GestorProdutoSimples {
	constructor(private readonly repositorioProdutoSimples: RepositorioProdutoSimples) {}

	async obterProdutosSimples(tipo: TipoProdutoSimples, busca: string): Promise<ProdutoSimples[]> {
		return this.repositorioProdutoSimples.obterProdutosSimples(tipo, busca);
	}
}
