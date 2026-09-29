import { RespostaHttp } from './resposta-http';

export interface ServicoHttp {
	get(url: string, headers?: Record<string, string>): Promise<RespostaHttp>;

	post(
		url: string,
		body?: unknown,
		headers?: Record<string, string>,
	): Promise<RespostaHttp>;

	put(
		url: string,
		body?: unknown,
		headers?: Record<string, string>,
	): Promise<RespostaHttp>;

	patch(
		url: string,
		body?: unknown,
		headers?: Record<string, string>,
	): Promise<RespostaHttp>;

	delete(
		url: string,
		headers?: Record<string, string>,
	): Promise<RespostaHttp>;
}
