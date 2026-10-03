import { ServicoHttp } from '../../infra/http/servico-http';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { API } from '../../infra/constantes';
import { RepositorioProdutoSimples } from './repositorio-produto-simples';
import { ProdutoSimples } from '../dominio/produto-simples';
import { TipoProdutoSimples } from '../dominio/tipo-produto-simples';

export class RepositorioProdutoSimplesNoServidor implements RepositorioProdutoSimples {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterProdutosSimples(tipo: TipoProdutoSimples, identificador: string): Promise<ProdutoSimples[]> {
		const produtosSimples: ProdutoSimples[] = [];

		try {
			const parametros = new URLSearchParams();
			parametros.set('identificador', identificador);
			parametros.set('tipo', tipo);

			const resultado = await this.servicoHttp.get(`${API}/produtos-simples?${parametros.toString()}`);

			resultado.data.produtosSimples.forEach((produto: any) => {
				produtosSimples.push(new ProdutoSimples(produto.id, produto.nome, produto.tipo, produto.descricao, produto.preco));
			});
		} catch (error) {
			throw new RepositorioError(`Erro ao obter produtos simples: ${error}`);
		}

		return produtosSimples;
	}
}
