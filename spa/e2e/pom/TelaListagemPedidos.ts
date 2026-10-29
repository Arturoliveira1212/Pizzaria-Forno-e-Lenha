import { expect, Locator, Response, type Page } from '@playwright/test';

export class TelaListagemPedidos {
	private readonly page: Page;

	private readonly listaPedidos: Locator;
	private readonly modalStatusPedido: Locator;
	private readonly selectStatusPedido: Locator;
	private readonly modalSaidaParaEntrega: Locator;
	private readonly selectEntregador: Locator;
	private readonly modalCancelamentoPedido: Locator;
	private readonly textareaMotivoCancelamento: Locator;

	constructor(page: Page) {
		this.page = page;

		this.listaPedidos = this.page.locator('#lista-pedidos');
		this.modalStatusPedido = this.page.locator('#modal-status-pedido');
		this.selectStatusPedido = this.page.locator('#novo-status-pedido');
		this.modalSaidaParaEntrega = this.page.locator('#modal-saida-para-entrega');
		this.selectEntregador = this.page.locator('#entregador-saida-para-entrega');
		this.modalCancelamentoPedido = this.page.locator('#modal-cancelamento-pedido');
		this.textareaMotivoCancelamento = this.page.locator('#motivo-cancelamento');
	}

	async abrir(): Promise<void> {
		const respostaApi = this.aguardarBuscaPedidos();
		await this.page.goto('/pedido');
		await respostaApi;
	}

	async alterarStatusDoPedido(idPedido: number, status: string): Promise<void> {
		await this.linhaPedido(idPedido).locator('.botao-alterar-status').click();
		await expect(this.modalStatusPedido).toBeVisible();
		await this.selectStatusPedido.selectOption(status);
		await this.modalStatusPedido.locator('button[type="submit"]').click();
	}

	async selecionarEntregadorERegistrarSaida(nome: string): Promise<void> {
		await this.selectEntregador.selectOption({ label: nome });

		const respostaApi = this.aguardarBuscaPedidos();
		await this.modalSaidaParaEntrega.locator('button[type="submit"]').click();
		await respostaApi;
	}

	async informarMotivoEConfirmarCancelamento(motivo: string): Promise<void> {
		await expect(this.modalCancelamentoPedido).toBeVisible();
		await this.textareaMotivoCancelamento.fill(motivo);

		const respostaApi = this.aguardarBuscaPedidos();
		await this.modalCancelamentoPedido.locator('button[type="submit"]').click();
		await respostaApi;
	}

	async confirmarCancelamentoSemMotivo(): Promise<void> {
		await expect(this.modalCancelamentoPedido).toBeVisible();
		await this.modalCancelamentoPedido.locator('button[type="submit"]').click();
	}

	async deveAparecerApenasOsEntregadores(nomes: string[]): Promise<void> {
		await expect(this.modalSaidaParaEntrega).toBeVisible();
		await expect(this.selectEntregador.locator('option:not([value=""])')).toHaveText(nomes);
	}

	async deveExibirStatusDoPedido(idPedido: number, status: string): Promise<void> {
		await expect(this.linhaPedido(idPedido).locator('.pedido-status')).toHaveText(status);
	}

	async deveExibirModalCancelamentoExigindoMotivo(): Promise<void> {
		await expect(this.modalCancelamentoPedido).toBeVisible();
		expect(await this.textareaMotivoCancelamento.evaluate((elemento: HTMLTextAreaElement) => elemento.validity.valueMissing)).toBe(true);
	}

	private linhaPedido(idPedido: number): Locator {
		return this.listaPedidos.locator('tr').filter({ has: this.page.locator('.pedido-id', { hasText: new RegExp(`^${idPedido}$`) }) });
	}

	private aguardarBuscaPedidos(): Promise<Response> {
		return this.page.waitForResponse((resposta) => {
			return resposta.url().includes('/pedidos') && resposta.request().method() === 'GET' && resposta.ok();
		});
	}
}
