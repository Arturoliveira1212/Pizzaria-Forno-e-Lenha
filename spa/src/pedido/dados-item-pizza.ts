import { TamanhoPizza } from '../pizza/dominio/tamanho-pizza';
import { DadosSaborEscolhido } from './dados-sabor-escolhido';

export type DadosItemPizza = {
	quantidade: number;
	tamanho: TamanhoPizza;
	preco: number;
	saboresEscolhidos: DadosSaborEscolhido[];
};
