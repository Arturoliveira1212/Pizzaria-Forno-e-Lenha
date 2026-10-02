import { TamanhoPizza } from './tamanho-pizza';

export class PrecoPorTamanho {
	constructor(
		public readonly id: number,
		public readonly tamanho: TamanhoPizza,
		public readonly preco: number,
	) {}
}
