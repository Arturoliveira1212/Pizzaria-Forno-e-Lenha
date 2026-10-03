import { RepositorioPizza } from '../../pizza/repositorio/repositorio-pizza';
import { Pizza } from './pizza';

export class GestorPizza {
	constructor(private readonly repositorioPizza: RepositorioPizza) {}

	async obterPizzasParaCardapio(busca: string): Promise<Pizza[]> {
		return this.repositorioPizza.obterPizzasParaCardapio(busca);
	}
}
