import { RepositorioPizza } from './repositorio-pizza';
import { Pizza } from '../dominio/pizza';
import { ServicoHttp } from '../../infra/http/servico-http';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { API } from '../../infra/constantes';
import { PrecoPorTamanho } from '../dominio/preco-por-tamanho';

export class RepositorioPizzaNoServidor implements RepositorioPizza {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterPizzasParaCardapio(identificador: string = ''): Promise<Pizza[]> {
		const pizzasParaCardapio: Pizza[] = [];

		try {
			const parametros = new URLSearchParams();
			parametros.set('identificador', identificador);
			parametros.set('cardapio', '1');

			const resultado = await this.servicoHttp.get(`${API}/pizzas?${parametros.toString()}`);

			resultado.data.pizzas.forEach((pizza: any) => {
				pizzasParaCardapio.push(
					new Pizza(
						pizza.id,
						pizza.nome,
						pizza.descricao,
						pizza.precosPorTamanho.map((preco: any) => new PrecoPorTamanho(preco.id, preco.tamanhoPizza, preco.preco)),
					),
				);
			});
		} catch (error) {
			throw new RepositorioError(`Erro ao obter pizzas para cardápio: ${error}`);
		}

		return pizzasParaCardapio;
	}
}
