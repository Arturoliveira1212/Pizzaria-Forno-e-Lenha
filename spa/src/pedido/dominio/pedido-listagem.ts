export class PedidoListagem {
	constructor(
		public readonly id: number,
		public readonly data: string,
		public readonly nomeCliente: string | null,
		public readonly tipoAtendimento: string,
		public readonly status: string,
		public readonly proximosStatus: string[],
		public readonly valorTotal: number,
	) {}
}
