import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { GestorAutenticacao } from '../dominio/gestor-autenticacao';
import { RepositorioAutenticacaoNoServidor } from '../repositorio/repositorio-autenticacao-no-servidor';
import { RepositorioUsuarioLogadoNoNavegador } from '../repositorio/repositorio-usuario-logado-no-navegador';
import { VisaoLogin } from '../view/visao-login';

export class ControladoraLogin {
	private readonly gestorAutenticacao: GestorAutenticacao;

	constructor(private readonly visaoLogin: VisaoLogin) {
		this.gestorAutenticacao = new GestorAutenticacao(new RepositorioAutenticacaoNoServidor(new ServicoHttpComFetch()), new RepositorioUsuarioLogadoNoNavegador());
	}

	async entrar(cpf: string, senha: string): Promise<void> {
		this.visaoLogin.limparErro();
		this.visaoLogin.desabilitarEnvio();

		try {
			await this.gestorAutenticacao.entrar(cpf, senha);
			this.visaoLogin.redirecionarParaHome();
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Ocorreu um erro ao entrar no sistema.';
			this.visaoLogin.exibirErro(mensagem);
			this.visaoLogin.habilitarEnvio();
		}
	}
}
