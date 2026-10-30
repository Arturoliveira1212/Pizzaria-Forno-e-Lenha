import { UsuarioLogado } from '../dominio/usuario-logado';

export interface VisaoMenuUsuario {
	exibirMenu(usuario: UsuarioLogado): void;

	ocultarMenu(): void;

	redirecionarParaLogin(): void;
}
