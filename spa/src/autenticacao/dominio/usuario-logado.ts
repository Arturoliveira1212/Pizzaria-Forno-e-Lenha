export class UsuarioLogado {
	constructor(
		public readonly id: number,
		public readonly nome: string,
		public readonly papel: string,
	) {}

	ehAdministrador(): boolean {
		return this.papel === 'ADMINISTRADOR';
	}
}
