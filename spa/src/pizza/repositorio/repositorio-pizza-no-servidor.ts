import { RepositorioPizza } from './repositorio-pizza';
import { ServicoHttp } from '../../infra/http/servico-http';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { API } from '../../infra/constantes';
import { PrecoPorTamanho } from '../dominio/preco-por-tamanho';
import { HttpError } from '../../infra/exceptions/http-error';
import { PizzaParaCardapio } from '../dominio/pizza-para-cardapio';
import { Ingrediente } from '../../ingrediente/dominio/ingrediente';

export class RepositorioPizzaNoServidor implements RepositorioPizza {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterPizzasParaCardapio(identificador: string = ''): Promise<PizzaParaCardapio[]> {
		const pizzasParaCardapio: PizzaParaCardapio[] = [];

		try {
			const parametros = new URLSearchParams();
			parametros.set('identificador', identificador);
			parametros.set('cardapio', '1');

			const resultado = await this.servicoHttp.get(`${API}/pizzas?${parametros.toString()}`);

			resultado.data.pizzas.forEach((pizza: any) => {
				pizzasParaCardapio.push(
					new PizzaParaCardapio(
						pizza.id,
						pizza.nome,
						pizza.descricao,
						pizza.precosPorTamanho.map((preco: any) => new PrecoPorTamanho(preco.id, preco.tamanhoPizza, preco.preco)),
						pizza.ingredientes.map((ingrediente: any) => new Ingrediente(ingrediente.id, ingrediente.nome)),
					),
				);
			});
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : 'Ocorreu um erro ao obter as pizzas para o cardápio.';
			throw new RepositorioError(mensagemErro);
		}

		return pizzasParaCardapio;
	}
}
