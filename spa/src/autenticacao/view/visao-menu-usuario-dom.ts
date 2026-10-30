import page from 'page';
import { ControladoraMenuUsuario } from '../controller/controladora-menu-usuario';
import { UsuarioLogado } from '../dominio/usuario-logado';
import { VisaoMenuUsuario } from './visao-menu-usuario';

export class VisaoMenuUsuarioDom implements VisaoMenuUsuario {
	private readonly menuNavegacao = document.querySelector<HTMLElement>('#menu-navegacao')!;
	private readonly menuUsuario = document.querySelector<HTMLElement>('#menu-usuario')!;
	private readonly nomeUsuario = document.querySelector<HTMLElement>('#nome-usuario')!;
	private readonly botaoSair = document.querySelector<HTMLButtonElement>('#botao-sair')!;
	private readonly controladoraMenuUsuario: ControladoraMenuUsuario;

	constructor() {
		this.controladoraMenuUsuario = new ControladoraMenuUsuario(this);
	}

	iniciar(): void {
		this.botaoSair.addEventListener('click', () => this.controladoraMenuUsuario.sair());
	}

	verificarUsuarioLogado(): boolean {
		return this.controladoraMenuUsuario.verificarUsuarioLogado();
	}

	encerrarSessaoExpirada(): void {
		this.controladoraMenuUsuario.encerrarSessaoExpirada();
	}

	exibirMenu(usuario: UsuarioLogado): void {
		this.nomeUsuario.textContent = usuario.nome;
		this.menuNavegacao.classList.remove('d-none');
		this.menuUsuario.classList.remove('d-none');
		this.menuUsuario.classList.add('d-flex');
	}

	ocultarMenu(): void {
		this.nomeUsuario.textContent = '';
		this.menuNavegacao.classList.add('d-none');
		this.menuUsuario.classList.add('d-none');
		this.menuUsuario.classList.remove('d-flex');
	}

	redirecionarParaLogin(): void {
		page('/login');
	}
}
