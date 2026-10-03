import { CategoriaCardapio } from './dominio/categoria-cardapio';

export type FiltroCardapio = {
	categoria: CategoriaCardapio;
	busca: string;
};
