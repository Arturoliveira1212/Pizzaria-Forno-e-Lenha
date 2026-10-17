import { expect, type Page } from '@playwright/test';

export class TelaRegistroPedido {
	constructor(private readonly page: Page) {}

	async abrir(): Promise<void> {
		await this.page.goto('/pedido/registrar');
	}

	async selecionarEtapaProdutos(): Promise<void> {
		await this.page.locator('[data-etapa="produtos"]').click();
	}

	async selecionarEtapaAtendimento(): Promise<void> {
		await this.page.locator('[data-etapa="atendimento"]').click();
	}

	async selecionarTamanhoPizza(nome: string, tamanho: 'P' | 'M' | 'G'): Promise<void> {
		const card = this.cardPizza(nome);
		const valores = { P: 'PEQUENA', M: 'MEDIA', G: 'GRANDE' };
		const input = card.locator(`input[value="${valores[tamanho]}"]`);
		const id = await input.getAttribute('id');

		await this.page.locator(`label[for="${id}"]`).click();
	}

	async buscarPizza(filtro: string): Promise<void> {
		await this.page.locator(`label[for="categoria-pizza-radio"]`).click();
		await this.page.locator('#filtro-cardapio').fill(filtro);
		await this.page.locator('#botao-filtrar-cardapio').click();
	}

	async deveAparecerTodasAsPizzasDoCardapio(): Promise<void> {
		const cards = this.page.locator('#secao-pizza .row .card-item');
		await expect(cards).toHaveCount(10);
	}

	async deveAparecerCardDaPizza(nome: string): Promise<void> {
		const card = this.page.locator('#secao-pizza .card-item').filter({ has: this.page.locator('.card-title', { hasText: nome }) });

		await expect(card).toHaveCount(1);
		await expect(card.locator('.card-title')).toHaveText(nome);
		await expect(card.locator('.card-text')).not.toBeEmpty();

		const tamanhos = card.locator('.tamanhos-pizza label');
		await expect(tamanhos).not.toHaveCount(0);

		for (const label of await tamanhos.all()) {
			await expect(label).toContainText('R$');
		}
	}

	async deveAparecerMensagemNenhumaPizzaEncontrada(): Promise<void> {
		const mensagem = this.page.locator('#secao-pizza .alert');

		await expect(mensagem).toHaveCount(1);
		await expect(mensagem).toContainText('Nenhuma pizza encontrada.');
	}

	async buscarBebida(filtro: string): Promise<void> {
		await this.page.locator(`label[for="categoria-bebida-radio"]`).click();
		await this.page.locator('#filtro-cardapio').fill(filtro);
		await this.page.locator('#botao-filtrar-cardapio').click();
	}

	async buscarSobremesa(filtro: string): Promise<void> {
		await this.page.locator(`label[for="categoria-sobremesa-radio"]`).click();
		await this.page.locator('#filtro-cardapio').fill(filtro);
		await this.page.locator('#botao-filtrar-cardapio').click();
	}

	async deveAparecerTodasAsBebidasDoCardapio(): Promise<void> {
		const cards = this.page.locator('#secao-bebida .row .card-item');
		await expect(cards).toHaveCount(3);
	}

	async deveAparecerCardDaBebida(nome: string): Promise<void> {
		const card = this.page.locator('#secao-bebida .card-item').filter({ has: this.page.locator('.card-title', { hasText: nome }) });

		await expect(card).toHaveCount(1);
		await expect(card.locator('.card-title')).toHaveText(nome);
		await expect(card.locator('.preco')).toContainText('R$');
	}

	async deveAparecerMensagemNenhumaBebidaEncontrada(): Promise<void> {
		const mensagem = this.page.locator('#secao-bebida .alert');

		await expect(mensagem).toHaveCount(1);
		await expect(mensagem).toContainText('Nenhuma bebida encontrada.');
	}

	async adicionarPizzaAoPedido(nome: string): Promise<void> {
		await this.cardPizza(nome).locator('.adicionar-item').click();
	}

	async adicionarProdutoSimplesAoPedido(nome: string): Promise<void> {
		const card = this.page.locator('#secao-bebida .card-item, #secao-sobremesa .card-item').filter({ has: this.page.locator('.card-title', { hasText: nome }) });

		await card.locator('.adicionar-item').click();
	}

	async deveAparecerPizzaNoResumo(nome: string, tamanho?: 'P' | 'M' | 'G'): Promise<void> {
		const item = this.itemResumo(nome);

		await expect(item).toHaveCount(1);
		if (tamanho) {
			await expect(item.locator('.nome-item')).toContainText(`Pizza ${tamanho}`);
		}

		await expect(item.locator('.quantidade-item')).toHaveValue('1');
	}

	async deveAparecerProdutoNoResumo(nome: string): Promise<void> {
		const item = this.itemResumo(nome);

		await expect(item).toHaveCount(1);
		await expect(item.locator('.nome-item')).toHaveText(nome);
		await expect(item.locator('.quantidade-item')).toHaveValue('1');
	}

	async deveTerQuantidadeNoResumo(nome: string, quantidade: string): Promise<void> {
		await expect(this.itemResumo(nome).locator('.quantidade-item')).toHaveValue(quantidade);
	}

	async aumentarQuantidadeDoItem(nome: string): Promise<void> {
		await this.itemResumo(nome).locator('.aumentar-quantidade').click();
	}

	async diminuirQuantidadeDoItem(nome: string): Promise<void> {
		await this.itemResumo(nome).locator('.diminuir-quantidade').click();
	}

	async removerItemDoResumo(nome: string): Promise<void> {
		await this.itemResumo(nome).locator('.remover-item').click();
	}

	async deveExibirCarrinhoVazio(): Promise<void> {
		await expect(this.page.locator('#aviso-vazio')).toBeVisible();
		await expect(this.page.locator('#titulo-carrinho')).toHaveText('Carrinho (0 itens)');
	}

	async selecionarTipoAtendimento(tipo: 'Entrega' | 'Retirada' | 'Balcão'): Promise<void> {
		const ids = { Entrega: 'tipo-entrega', Retirada: 'tipo-retirada', Balcão: 'tipo-balcao' };
		await this.page.locator(`label[for="${ids[tipo]}"]`).click();
	}

	async deveEstarDesabilitadoTipoClienteConsumidorFinal(): Promise<void> {
		await expect(this.page.locator('#tipo-consumidor-final')).toBeDisabled();
	}

	async selecionarTipoCliente(tipo: 'Consumidor Final' | 'Cliente'): Promise<void> {
		const id = tipo === 'Consumidor Final' ? 'tipo-consumidor-final' : 'tipo-cliente-identificado';
		await this.page.locator(`label[for="${id}"]`).click();
	}

	async deveExibirPedidoSemClienteVinculado(): Promise<void> {
		await expect(this.page.locator('#secao-consumidor-final')).toBeVisible();
		await expect(this.page.locator('#secao-consumidor-final')).toContainText('Pedido sem cliente vinculado.');
	}

	async deveExibirSecaoDeClientes(): Promise<void> {
		await expect(this.page.locator('#secao-cliente')).toBeVisible();
	}

	async buscarCliente(filtro: string): Promise<void> {
		await this.page.locator('#filtro-cliente').fill(filtro);
		await this.page.locator('#botao-filtrar-cliente').click();
	}

	async selecionarCliente(nome: string): Promise<void> {
		const cliente = this.page.locator('#lista-clientes label').filter({ hasText: nome });
		await cliente.click();
	}

	async deveExibirTaxaDeEntrega(): Promise<void> {
		await expect(this.page.locator('#secao-entrega')).toBeVisible();
		await expect(this.page.locator('#valor-entrega')).toHaveText('R$ 10,00');
	}

	async deveExibirDescontoFidelidade(): Promise<void> {
		await expect(this.page.locator('#secao-fidelidade')).toBeVisible();
		await expect(this.page.locator('#valor-desconto-fidelidade')).toHaveText(/-R\$\s*[1-9]/);
	}

	async deveNaoExibirDescontoFidelidade(): Promise<void> {
		await expect(this.page.locator('#secao-fidelidade')).toBeHidden();
	}

	async deveExibirDescontoPromocao(): Promise<void> {
		await expect(this.page.locator('#secao-promocoes')).toBeVisible();
		await expect(this.page.locator('#valor-desconto-promocao')).toHaveText(/-R\$\s*[1-9]/);
	}

	async registrarPedido(): Promise<void> {
		await this.page.locator('#botao-registrar-pedido').click();
	}

	async deveRedirecionarParaInicio(): Promise<void> {
		await expect(this.page).toHaveURL(/\/$/);
	}

	async deveExibirErroNoPedido(mensagem: string): Promise<void> {
		await expect(this.page.locator('#titulo-carrinho .alert')).toContainText(mensagem);
	}

	private cardPizza(nome: string) {
		return this.page.locator('#secao-pizza .card-item').filter({ has: this.page.locator('.card-title', { hasText: nome }) });
	}

	private itemResumo(nome: string) {
		return this.page.locator('#lista-itens-pedido .item-pedido').filter({ hasText: nome });
	}
}
