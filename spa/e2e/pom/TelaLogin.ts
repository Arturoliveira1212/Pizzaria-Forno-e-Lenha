import { expect, Locator, type Page } from '@playwright/test';

export class TelaLogin {
	private readonly page: Page;

	private readonly inputCpf: Locator;
	private readonly inputSenha: Locator;
	private readonly botaoEntrar: Locator;
	private readonly alertaErro: Locator;
	private readonly nomeUsuario: Locator;
	private readonly botaoSair: Locator;

	constructor(page: Page) {
		this.page = page;

		this.inputCpf = this.page.locator('#cpf');
		this.inputSenha = this.page.locator('#senha');
		this.botaoEntrar = this.page.locator('#botao-entrar');
		this.alertaErro = this.page.locator('#erro-login');
		this.nomeUsuario = this.page.locator('#nome-usuario');
		this.botaoSair = this.page.locator('#botao-sair');
	}

	async abrir(): Promise<void> {
		await this.page.goto('/login');
	}

	async abrirPagina(caminho: string): Promise<void> {
		await this.page.goto(caminho);
	}

	async entrar(cpf: string, senha: string): Promise<void> {
		await this.inputCpf.fill(cpf);
		await this.inputSenha.fill(senha);
		await this.botaoEntrar.click();
	}

	async sair(): Promise<void> {
		await this.botaoSair.click();
	}

	async deveEstarNaTelaDeLogin(): Promise<void> {
		await expect(this.page).toHaveURL(/\/login$/);
		await expect(this.botaoEntrar).toBeVisible();
	}

	async deveEstarNaPaginaInicial(): Promise<void> {
		await expect(this.page).toHaveURL(/\/$/);
	}

	async deveExibirNomeDoUsuario(nome: string): Promise<void> {
		await expect(this.nomeUsuario).toHaveText(nome);
	}

	async deveNaoExibirNomeDoUsuario(): Promise<void> {
		await expect(this.nomeUsuario).not.toBeVisible();
	}

	async deveExibirErro(mensagem: string): Promise<void> {
		await expect(this.alertaErro).toHaveText(mensagem);
	}
}
