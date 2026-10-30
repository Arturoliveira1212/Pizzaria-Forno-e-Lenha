export class DetalhesAdicionalSabor {
	constructor(
		public readonly nome: string,
		public readonly quantidade: number,
	) {}
}

export class DetalhesSaborEscolhido {
	constructor(
		public readonly nome: string,
		public readonly ingredientesRemovidos: string[],
		public readonly ingredientesSubstitutos: string[],
		public readonly adicionais: DetalhesAdicionalSabor[],
	) {}
}

export class DetalhesItemPedido {
	constructor(
		public readonly descricao: string,
		public readonly quantidade: number,
		public readonly precoUnitario: number,
		public readonly precoTotal: number,
		public readonly sabores: DetalhesSaborEscolhido[],
	) {}
}

export class DetalhesPedido {
	constructor(
		public readonly id: number,
		public readonly data: string,
		public readonly tipoAtendimento: string,
		public readonly status: string,
		public readonly nomeAtendente: string,
		public readonly nomeCliente: string | null,
		public readonly telefoneCliente: string | null,
		public readonly endereco: string | null,
		public readonly itens: DetalhesItemPedido[],
		public readonly subtotal: number,
		public readonly valorDescontoPromocao: number,
		public readonly valorDescontoFidelidade: number,
		public readonly taxaEntrega: number,
		public readonly valorTotal: number,
		public readonly nomeEntregador: string | null,
		public readonly dataSaida: string | null,
		public readonly dataChegada: string | null,
		public readonly motivoCancelamento: string | null,
		public readonly dataCancelamento: string | null,
	) {}
}
