import { DominioError } from '../../infra/exceptions/dominio-error';
import { RepositorioAutenticacao } from '../repositorio/repositorio-autenticacao';
import { RepositorioUsuarioLogado } from '../repositorio/repositorio-usuario-logado';
import { UsuarioLogado } from './usuario-logado';

export class GestorAutenticacao {
	constructor(
		private readonly repositorioAutenticacao: RepositorioAutenticacao,
		private readonly repositorioUsuarioLogado: RepositorioUsuarioLogado,
	) {}

	async entrar(cpf: string, senha: string): Promise<UsuarioLogado> {
		const numeroCpf = cpf.replace(/\D/g, '');

		const erros: string[] = [];
		if (numeroCpf.length !== 11) {
			erros.push('O CPF deve conter 11 dígitos.');
		}

		if (senha === '') {
			erros.push('A senha é obrigatória.');
		}

		if (erros.length > 0) {
			throw new DominioError(erros.join(', '));
		}

		const usuario = await this.repositorioAutenticacao.entrar(numeroCpf, senha);
		this.repositorioUsuarioLogado.salvar(usuario);

		return usuario;
	}

	async sair(): Promise<void> {
		try {
			await this.repositorioAutenticacao.sair();
		} finally {
			this.repositorioUsuarioLogado.remover();
		}
	}

	obterUsuarioLogado(): UsuarioLogado | null {
		return this.repositorioUsuarioLogado.obter();
	}

	esquecerUsuarioLogado(): void {
		this.repositorioUsuarioLogado.remover();
	}
}
