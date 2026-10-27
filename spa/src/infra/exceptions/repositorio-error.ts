export class RepositorioError extends Error {
	constructor(
		mensagem: string,
		public readonly data?: any,
	) {
		super(mensagem);
		this.name = 'RepositorioError';
		this.data = data;
	}
}
