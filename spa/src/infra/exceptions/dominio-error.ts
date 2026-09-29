export class DominioError extends Error {
	constructor(mensagem: string) {
		super(mensagem);
		this.name = 'DominioError';
	}
}
