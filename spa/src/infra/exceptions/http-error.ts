export class HttpError extends Error {
	public readonly status: number;
	public readonly data: any;

	constructor(mensagem: string, status: number, data: any) {
		super(mensagem);
		this.name = 'HttpError';
		this.status = status;
		this.data = data;
	}
}
