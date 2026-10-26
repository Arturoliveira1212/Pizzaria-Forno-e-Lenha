import { TamanhoPizza } from './tamanho-pizza';

export class QuantidadePorTamanho {
	constructor(
		public readonly id: number,
		public readonly tamanhoPizza: TamanhoPizza,
		public readonly quantidade: number,
	) {}
}
