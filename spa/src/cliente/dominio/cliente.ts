export class Cliente {
	constructor(
		public readonly id: number,
		public readonly nome: string = '',
		public readonly telefone: string = '',
		public readonly cpf: string = '',
	) {}
}
