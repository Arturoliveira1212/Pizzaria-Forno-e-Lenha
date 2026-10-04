export class ResumoDescontosPedido {
	public constructor(
		public readonly valorDescontoPromocao: number,
		public readonly valorDescontoFidelidade: number,
		public readonly valorTotal: number,
	) {}
}
