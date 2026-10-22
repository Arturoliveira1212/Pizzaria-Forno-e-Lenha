import { TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { PapelIngrediente } from '../dominio/papel-ingrediente';

export interface VisaoRegistroPedido {
	iniciar(): void;

	redirecionarParaHome(): void;
}

export enum CategoriaCardapio {
	PIZZA = 'pizza',
	BEBIDA = 'bebida',
	SOBREMESA = 'sobremesa',
}

export enum EtapaPedido {
	PRODUTOS = 'produtos',
	ATENDIMENTO = 'atendimento',
}

export enum SaborPizza {
	PRIMEIRO_SABOR = 'primeiro-sabor',
	SEGUNDO_SABOR = 'segundo-sabor',
}

export enum TipoCliente {
	CLIENTE_IDENTIFICADO = 'cliente-identificado',
	CONSUMIDOR_FINAL = 'consumidor-final',
}

export enum TipoPizza {
	INTEIRA = 'inteira',
	MEIA = 'meia',
}

export type DadosIngredienteEscolhido = {
	idIngrediente: number;
	papel: PapelIngrediente;
};

export type DadosItemPizza = {
	quantidade: number;
	tamanho: TamanhoPizza;
	preco: number;
	saboresEscolhidos: DadosSaborEscolhido[];
};

export type DadosItemSimples = {
	quantidade: number;
	idProduto: number;
	nome: string;
	preco: number;
};

export type DadosSaborEscolhido = {
	idPizza: number;
	nome: string;
	ingredientesEscolhidos: DadosIngredienteEscolhido[];
};

export type FiltroCardapio = {
	categoria: CategoriaCardapio;
	busca: string;
};
