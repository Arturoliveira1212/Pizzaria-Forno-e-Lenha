import { Pizza } from '../dominio/pizza';

export interface RepositorioPizza {
	obterPizzasParaCardapio(identificador: string): Promise<Pizza[]>;
}
