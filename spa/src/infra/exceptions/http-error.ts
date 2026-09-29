export class HttpError extends Error {
	public readonly status: number;
	public readonly data: unknown;

	constructor(mensagem: string, status: number, data: unknown) {
		super(mensagem);
		this.name = 'HttpError';
		this.status = status;
		this.data = data;
	}
}
