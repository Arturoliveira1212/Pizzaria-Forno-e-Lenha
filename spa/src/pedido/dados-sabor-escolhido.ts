import { DadosIngredienteEscolhido } from './dados-ingrediente-escolhido';

export type DadosSaborEscolhido = {
	idPizza: number;
	nome: string;
	ingredientesEscolhidos: DadosIngredienteEscolhido[];
};
