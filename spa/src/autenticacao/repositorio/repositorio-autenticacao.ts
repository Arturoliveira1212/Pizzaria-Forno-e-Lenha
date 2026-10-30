import { UsuarioLogado } from '../dominio/usuario-logado';

export interface RepositorioAutenticacao {
	entrar(cpf: string, senha: string): Promise<UsuarioLogado>;

	sair(): Promise<void>;
}
