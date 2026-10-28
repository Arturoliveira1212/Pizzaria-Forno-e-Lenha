import { API } from '../../infra/constantes';
import { HttpError } from '../../infra/exceptions/http-error';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { ServicoHttp } from '../../infra/http/servico-http';
import { Entregador } from '../dominio/entregador';
import { RepositorioEntregador } from './repositorio-entregador';

export class RepositorioEntregadorNoServidor implements RepositorioEntregador {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterEntregadores(): Promise<Entregador[]> {
		const entregadores: Entregador[] = [];

		try {
			const resultado = await this.servicoHttp.get(`${API}/entregadores`);

			resultado.data.entregadores.forEach((entregador: any) => {
				entregadores.push(new Entregador(entregador.id, entregador.nome));
			});
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : 'Ocorreu um erro ao obter os entregadores.';
			throw new RepositorioError(mensagemErro);
		}

		return entregadores;
	}
}
