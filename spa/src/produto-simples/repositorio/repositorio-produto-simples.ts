import { ProdutoSimples } from '../dominio/produto-simples';
import { TipoProdutoSimples } from '../dominio/tipo-produto-simples';

export interface RepositorioProdutoSimples {
	obterProdutosSimples(tipo: TipoProdutoSimples, identificador: string): Promise<ProdutoSimples[]>;
}
