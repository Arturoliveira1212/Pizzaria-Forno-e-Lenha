import page from 'page';
import { ControladoraLogin } from '../controller/controladora-login';
import { VisaoLogin } from './visao-login';

export class VisaoLoginDom implements VisaoLogin {
	private readonly pagina = document.querySelector<HTMLElement>('#pagina-login')!;
	private readonly formularioLogin = this.pagina.querySelector<HTMLFormElement>('#formulario-login')!;
	private readonly inputCpf = this.pagina.querySelector<HTMLInputElement>('#cpf')!;
	private readonly inputSenha = this.pagina.querySelector<HTMLInputElement>('#senha')!;
	private readonly botaoEntrar = this.pagina.querySelector<HTMLButtonElement>('#botao-entrar')!;
	private readonly alertaErro = this.pagina.querySelector<HTMLElement>('#erro-login')!;
	private readonly controladoraLogin: ControladoraLogin;

	constructor() {
		this.controladoraLogin = new ControladoraLogin(this);
	}

	iniciar(): void {
		this.formularioLogin.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraLogin.entrar(this.inputCpf.value, this.inputSenha.value);
		});
		this.inputCpf.focus();
	}

	exibirErro(mensagem: string): void {
		this.alertaErro.textContent = mensagem;
		this.alertaErro.classList.remove('d-none');
		this.inputSenha.value = '';
		this.inputSenha.focus();
	}

	limparErro(): void {
		this.alertaErro.textContent = '';
		this.alertaErro.classList.add('d-none');
	}

	desabilitarEnvio(): void {
		this.botaoEntrar.disabled = true;
	}

	habilitarEnvio(): void {
		this.botaoEntrar.disabled = false;
	}

	redirecionarParaHome(): void {
		page('/');
	}
}
