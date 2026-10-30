import { beforeEach, describe, expect, it } from 'vitest';
import { GestorAutenticacao } from '../src/autenticacao/dominio/gestor-autenticacao';
import { UsuarioLogado } from '../src/autenticacao/dominio/usuario-logado';
import { RepositorioAutenticacao } from '../src/autenticacao/repositorio/repositorio-autenticacao';
import { RepositorioUsuarioLogado } from '../src/autenticacao/repositorio/repositorio-usuario-logado';
import { DominioError } from '../src/infra/exceptions/dominio-error';

class RepositorioAutenticacaoEmMemoria implements RepositorioAutenticacao {
	async entrar(cpf: string, senha: string): Promise<UsuarioLogado> {
		return new UsuarioLogado(2, 'Mariana Silva', 'ATENDENTE');
	}

	async sair(): Promise<void> {}
}

class RepositorioUsuarioLogadoEmMemoria implements RepositorioUsuarioLogado {
	private usuario: UsuarioLogado | null = null;

	salvar(usuario: UsuarioLogado): void {
		this.usuario = usuario;
	}

	obter(): UsuarioLogado | null {
		return this.usuario;
	}

	remover(): void {
		this.usuario = null;
	}
}

describe('GestorAutenticacao', () => {
	let gestor: GestorAutenticacao;

	beforeEach(() => {
		gestor = new GestorAutenticacao(new RepositorioAutenticacaoEmMemoria(), new RepositorioUsuarioLogadoEmMemoria());
	});

	describe('entrar', () => {
		it('lança erro quando o CPF não possui 11 dígitos', async () => {
			await expect(gestor.entrar('123', 'atendente123')).rejects.toThrow(new DominioError('O CPF deve conter 11 dígitos.'));
		});

		it('guarda o usuário logado quando os dados são válidos', async () => {
			await gestor.entrar('987.654.321-00', 'atendente123');

			expect(gestor.obterUsuarioLogado()).toEqual(new UsuarioLogado(2, 'Mariana Silva', 'ATENDENTE'));
		});
	});
});
