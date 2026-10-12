import { TamanhoPizza } from '../pizza/dominio/tamanho-pizza';
import { PapelIngrediente } from './dominio/papel-ingrediente';

export type DadosItemPizza = {
	quantidade: number;
	tamanho: TamanhoPizza;
	preco: number;
	saboresEscolhidos: {
		idPizza: number;
		nome: string;
		ingredientesEscolhidos: {
			idIngrediente: number;
			papel: PapelIngrediente;
		}[];
	}[];
};
