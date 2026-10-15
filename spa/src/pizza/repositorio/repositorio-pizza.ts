import { Pizza } from '../dominio/pizza';
import { PizzaParaCardapio } from '../dominio/pizza-para-cardapio';

export interface RepositorioPizza {
	obterPizzasParaCardapio(identificador: string): Promise<PizzaParaCardapio[]>;

	obterPizzaPorId(idPizza: number): Promise<Pizza>;
}
