export class ResumoValoresPedido {
	public constructor(
		public readonly valorDescontoPromocao: number,
		public readonly valorDescontoFidelidade: number,
		public readonly valorTotalDescontos: number,
		public readonly taxaEntrega: number,
		public readonly valorTotal: number,
	) {}
}
