export class RepositorioError extends Error {
	constructor(mensagem: string) {
		super(mensagem);
		this.name = 'RepositorioError';
	}
}
