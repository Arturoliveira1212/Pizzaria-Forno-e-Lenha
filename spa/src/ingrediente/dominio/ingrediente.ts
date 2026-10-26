export class Ingrediente {
	constructor(
		public readonly id: number,
		public readonly nome: string = '',
		public readonly estoque: number = 0,
	) {}

	temEstoqueSuficiente(quantidade: number): boolean {
		return this.estoque >= quantidade;
	}
}
