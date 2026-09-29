export class Pedido {
	public constructor(public readonly id: number) {
		this.validar();
	}

	public validar(): void {}
}
