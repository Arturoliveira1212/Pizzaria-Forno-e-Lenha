// fetch-servico-http.ts
import { ServicoHttp } from './servico-http';
import { RespostaHttp } from './resposta-http';
import { HttpError } from '../exceptions/http-error';

export class ServicoHttpComFetch implements ServicoHttp {
	constructor() {}

	get(url: string, headers?: Record<string, string>) {
		return this.realizarRequisicao('GET', url, undefined, headers);
	}

	post(url: string, body?: unknown, headers?: Record<string, string>) {
		return this.realizarRequisicao('POST', url, body, headers);
	}

	put(url: string, body?: unknown, headers?: Record<string, string>) {
		return this.realizarRequisicao('PUT', url, body, headers);
	}

	patch(url: string, body?: unknown, headers?: Record<string, string>) {
		return this.realizarRequisicao('PATCH', url, body, headers);
	}

	delete(url: string, headers?: Record<string, string>) {
		return this.realizarRequisicao('DELETE', url, undefined, headers);
	}

	private async realizarRequisicao(
		metodo: string,
		url: string,
		body?: unknown,
		headers?: Record<string, string>,
	): Promise<RespostaHttp> {
		const resposta = await fetch(url, {
			method: metodo,
			headers: {
				'Content-Type': 'application/json',
				...headers,
			},
			body: body !== undefined ? JSON.stringify(body) : undefined,
		});

		const data = await resposta.json();

		if (!resposta.ok) {
			throw new HttpError(`Erro ao realizar requisição`, resposta.status, data);
		}

		return { status: resposta.status, data: data?.data };
	}
}
