import { RepositorioPizza } from './repositorio-pizza';
import { ServicoHttp } from '../../infra/http/servico-http';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { API } from '../../infra/constantes';
import { PrecoPorTamanho } from '../dominio/preco-por-tamanho';
import { HttpError } from '../../infra/exceptions/http-error';
import { PizzaParaCardapio } from '../dominio/pizza-para-cardapio';
import { Pizza } from '../dominio/pizza';
import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { IngredientePadrao } from '../dominio/ingrediente-padrao';
import { IngredienteAdicional } from '../dominio/ingrediente-adicional';
import { IngredienteSubstituto } from '../dominio/ingrediente-substituto';

export class RepositorioPizzaNoServidor implements RepositorioPizza {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterPizzasParaCardapio(identificador: string = ''): Promise<PizzaParaCardapio[]> {
		const pizzasParaCardapio: PizzaParaCardapio[] = [];

		try {
			const parametros = new URLSearchParams();
			parametros.set('identificador', identificador);
			parametros.set('cardapio', '1');

			const resultado = await this.servicoHttp.get(`${API}/pizzas?${parametros.toString()}`);
			resultado.data.pizzas.forEach((pizza: any) => pizzasParaCardapio.push(this.criarPizzaParaCardapio(pizza)));
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : 'Ocorreu um erro ao obter as pizzas para o cardápio.';
			throw new RepositorioError(mensagemErro);
		}

		return pizzasParaCardapio;
	}

	private criarPizzaParaCardapio(pizza: any): PizzaParaCardapio {
		return new PizzaParaCardapio(
			pizza.id,
			pizza.nome,
			pizza.descricao,
			pizza.precosPorTamanho.map((preco: any) => new PrecoPorTamanho(preco.id, preco.tamanhoPizza, preco.preco)),
			pizza.ingredientes.map((ingrediente: any) => new Ingrediente(ingrediente.id, ingrediente.nome)),
		);
	}

	async obterPizzas(identificador: string = ''): Promise<Pizza[]> {
		const pizzas: Pizza[] = [];

		try {
			const parametros = new URLSearchParams();
			parametros.set('identificador', identificador);

			const resultado = await this.servicoHttp.get(`${API}/pizzas?${parametros.toString()}`);
			resultado.data.pizzas.forEach((pizza: any) => pizzas.push(this.criarPizza(pizza)));
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : 'Ocorreu um erro ao obter as pizzas.';
			throw new RepositorioError(mensagemErro);
		}

		return pizzas;
	}

	async obterPizzaPorId(idPizza: number): Promise<Pizza> {
		try {
			const resultado = await this.servicoHttp.get(`${API}/pizzas/${idPizza}`);
			const pizza = resultado.data.pizza;

			return this.criarPizza(pizza);
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.message : 'Ocorreu um erro ao obter a pizza.';
			throw new RepositorioError(mensagemErro);
		}
	}

	private criarPizza(pizza: any): Pizza {
		const ingredientesPadrao = pizza.ingredientesPadrao.map((ingredientePadrao: any) => {
			return new IngredientePadrao(
				ingredientePadrao.id,
				new Ingrediente(ingredientePadrao.ingrediente.id, ingredientePadrao.ingrediente.nome),
				ingredientePadrao.removivel,
				ingredientePadrao.substituivel,
			);
		});
		const ingredientesSubstitutos = pizza.ingredientesSubstitutos.map((ingredienteSubstituto: any) => {
			return new IngredienteSubstituto(ingredienteSubstituto.id, new Ingrediente(ingredienteSubstituto.ingrediente.id, ingredienteSubstituto.ingrediente.nome));
		});
		const adicionais = pizza.adicionais.map((adicional: any) => {
			return new IngredienteAdicional(adicional.id, new Ingrediente(adicional.ingrediente.id, adicional.ingrediente.nome), adicional.preco);
		});
		const precosPorTamanho = pizza.precosPorTamanho.map((precoPorTamanho: any) => {
			return new PrecoPorTamanho(precoPorTamanho.id, precoPorTamanho.tamanhoPizza, precoPorTamanho.preco);
		});

		return new Pizza(pizza.id, pizza.nome, pizza.descricao, ingredientesPadrao, ingredientesSubstitutos, adicionais, precosPorTamanho);
	}
}
