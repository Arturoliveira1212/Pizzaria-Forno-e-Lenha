import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { GestorAutenticacao } from '../dominio/gestor-autenticacao';
import { RepositorioAutenticacaoNoServidor } from '../repositorio/repositorio-autenticacao-no-servidor';
import { RepositorioUsuarioLogadoNoNavegador } from '../repositorio/repositorio-usuario-logado-no-navegador';
import { VisaoMenuUsuario } from '../view/visao-menu-usuario';

export class ControladoraMenuUsuario {
	private readonly gestorAutenticacao: GestorAutenticacao;

	constructor(private readonly visaoMenuUsuario: VisaoMenuUsuario) {
		this.gestorAutenticacao = new GestorAutenticacao(new RepositorioAutenticacaoNoServidor(new ServicoHttpComFetch()), new RepositorioUsuarioLogadoNoNavegador());
	}

	verificarUsuarioLogado(): boolean {
		const usuario = this.gestorAutenticacao.obterUsuarioLogado();
		if (usuario === null) {
			this.visaoMenuUsuario.ocultarMenu();
			return false;
		}

		this.visaoMenuUsuario.exibirMenu(usuario);
		return true;
	}

	verificarAdministrador(): boolean {
		return this.gestorAutenticacao.obterUsuarioLogado()?.ehAdministrador() ?? false;
	}

	encerrarSessaoExpirada(): void {
		this.gestorAutenticacao.esquecerUsuarioLogado();
		this.visaoMenuUsuario.ocultarMenu();
		this.visaoMenuUsuario.redirecionarParaLogin();
	}

	async sair(): Promise<void> {
		try {
			await this.gestorAutenticacao.sair();
		} catch (erro) {
			console.error(erro);
		}

		this.visaoMenuUsuario.ocultarMenu();
		this.visaoMenuUsuario.redirecionarParaLogin();
	}
}
