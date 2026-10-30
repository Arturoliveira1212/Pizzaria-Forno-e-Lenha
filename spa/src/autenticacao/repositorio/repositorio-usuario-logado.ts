import { UsuarioLogado } from '../dominio/usuario-logado';

export interface RepositorioUsuarioLogado {
	salvar(usuario: UsuarioLogado): void;

	obter(): UsuarioLogado | null;

	remover(): void;
}
