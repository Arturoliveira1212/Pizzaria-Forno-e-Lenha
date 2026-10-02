import { PrecoPorTamanho } from './preco-por-tamanho';

export class Pizza {
	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly descricao: string,
		public readonly precosPorTamanho: PrecoPorTamanho[],
		public readonly imagem: string,
	) {}
}
