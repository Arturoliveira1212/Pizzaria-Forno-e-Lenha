import { API } from '../../infra/constantes';
import { HttpError } from '../../infra/exceptions/http-error';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { ServicoHttp } from '../../infra/http/servico-http';
import { UsuarioLogado } from '../dominio/usuario-logado';
import { RepositorioAutenticacao } from './repositorio-autenticacao';

export class RepositorioAutenticacaoNoServidor implements RepositorioAutenticacao {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async entrar(cpf: string, senha: string): Promise<UsuarioLogado> {
		try {
			const resultado = await this.servicoHttp.post(`${API}/login`, { cpf, senha });
			const usuario = resultado.data.usuario;

			return new UsuarioLogado(usuario.id, usuario.nome, usuario.papel);
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao entrar no sistema.');
		}
	}

	async sair(): Promise<void> {
		try {
			await this.servicoHttp.post(`${API}/logout`);
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao sair do sistema.');
		}
	}

	private tratarErro(error: any, mensagemPadrao: string): never {
		const mensagemErro = error instanceof HttpError ? (error.data?.erros?.join(', ') ?? mensagemPadrao) : mensagemPadrao;
		throw new RepositorioError(mensagemErro);
	}
}
