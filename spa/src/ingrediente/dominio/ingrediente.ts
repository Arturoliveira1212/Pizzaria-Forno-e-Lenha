export class Ingrediente {
	constructor(
		public readonly id: number,
		public readonly nome: string = '',
		public readonly estoque: number = 0,
	) {}
}
