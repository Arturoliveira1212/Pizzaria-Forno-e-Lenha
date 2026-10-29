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

	obterNomeClienteAbreviado(): string {
		if (this.nomeCliente === null) {
			return 'Consumidor final';
		}

		const nomes = this.nomeCliente.split(' ');
		if (nomes.length === 1) {
			return nomes[0];
		}

		return `${nomes[0]} ${nomes[1].charAt(0)}.`;
	}
}
