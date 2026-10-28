import { expect, Locator, Response, type Page } from '@playwright/test';

type Categoria = 'pizza' | 'bebida' | 'sobremesa';
type Tamanho = 'P' | 'M' | 'G';
type TipoAtendimento = 'Entrega' | 'Retirada' | 'Balcão';
type TipoCliente = 'Consumidor Final' | 'Cliente';
type Sabor = 1 | 2;

export class TelaRegistroPedido {
	private readonly page: Page;

	private readonly botaoEtapaProdutos: Locator;
	private readonly botaoEtapaAtendimento: Locator;
	private readonly filtroCardapio: Locator;
	private readonly botaoFiltrarCardapio: Locator;
	private readonly listaItensPedido: Locator;
	private readonly labelTipoAtendimentoEntrega: Locator;
	private readonly labelTipoAtendimentoRetirada: Locator;
	private readonly labelTipoAtendimentoBalcao: Locator;
	private readonly labelTipoClienteConsumidorFinal: Locator;
	private readonly labelTipoClienteIdentificado: Locator;
	private readonly filtroCliente: Locator;
	private readonly botaoFiltrarCliente: Locator;
	private readonly listaClientes: Locator;
	private readonly botaoRegistrarPedido: Locator;
	private readonly secaoPizza: Locator;
	private readonly secaoBebida: Locator;
	private readonly avisoCarrinhoVazio: Locator;
	private readonly tituloCarrinho: Locator;
	private readonly secaoConsumidorFinal: Locator;
	private readonly secaoCliente: Locator;
	private readonly secaoEntrega: Locator;
	private readonly valorEntrega: Locator;
	private readonly secaoFidelidade: Locator;
	private readonly secaoPromocoes: Locator;
	private readonly valorDescontoFidelidade: Locator;
	private readonly valorDescontoPromocao: Locator;
	private readonly modalPersonalizacaoPizza: Locator;
	private readonly botaoFecharModalPersonalizacaoPizza: Locator;
	private readonly listaIngredientesPrimeiroSabor: Locator;
	private readonly listaAdicionaisPrimeiroSabor: Locator;
	private readonly listaIngredientesSegundoSabor: Locator;
	private readonly listaAdicionaisSegundoSabor: Locator;
	private readonly valorTotalPersonalizacaoPizza: Locator;
	private readonly botaoAdicionarPersonalizacaoPizza: Locator;
	private readonly valorTotalPedido: Locator;

	constructor(page: Page) {
		this.page = page;

		this.botaoEtapaProdutos = this.page.getByTestId('botao-etapa-produtos');
		this.botaoEtapaAtendimento = this.page.getByTestId('botao-etapa-atendimento');
		this.filtroCardapio = this.page.locator('#filtro-cardapio');
		this.botaoFiltrarCardapio = this.page.locator('#botao-filtrar-cardapio');
		this.listaItensPedido = this.page.locator('#lista-itens-pedido');
		this.labelTipoAtendimentoEntrega = this.page.getByTestId('tipo-atendimento-entrega');
		this.labelTipoAtendimentoRetirada = this.page.getByTestId('tipo-atendimento-retirada');
		this.labelTipoAtendimentoBalcao = this.page.getByTestId('tipo-atendimento-balcao');
		this.labelTipoClienteConsumidorFinal = this.page.getByTestId('tipo-cliente-consumidor-final');
		this.labelTipoClienteIdentificado = this.page.getByTestId('tipo-cliente-identificado');
		this.filtroCliente = this.page.locator('#filtro-cliente');
		this.botaoFiltrarCliente = this.page.locator('#botao-filtrar-cliente');
		this.listaClientes = this.page.locator('#lista-clientes');
		this.botaoRegistrarPedido = this.page.locator('#botao-registrar-pedido');
		this.secaoPizza = this.page.locator('#secao-pizza');
		this.secaoBebida = this.page.locator('#secao-bebida');
		this.avisoCarrinhoVazio = this.page.locator('#aviso-vazio');
		this.tituloCarrinho = this.page.locator('#titulo-carrinho');
		this.secaoConsumidorFinal = this.page.locator('#secao-consumidor-final');
		this.secaoCliente = this.page.locator('#secao-cliente');
		this.secaoEntrega = this.page.locator('#secao-entrega');
		this.valorEntrega = this.page.locator('#valor-entrega');
		this.secaoFidelidade = this.page.locator('#secao-fidelidade');
		this.secaoPromocoes = this.page.locator('#secao-promocoes');
		this.valorDescontoFidelidade = this.page.locator('#valor-desconto-fidelidade');
		this.valorDescontoPromocao = this.page.locator('#valor-desconto-promocao');
		this.modalPersonalizacaoPizza = this.page.locator('#modal-personalizacao-pizza');
		this.botaoFecharModalPersonalizacaoPizza = this.page.getByTestId('botao-fechar-modal-personalizacao');
		this.listaIngredientesPrimeiroSabor = this.page.locator('#lista-ingredientes-primeiro-sabor');
		this.listaAdicionaisPrimeiroSabor = this.page.locator('#lista-adicionais-primeiro-sabor');
		this.listaIngredientesSegundoSabor = this.page.locator('#lista-ingredientes-segundo-sabor');
		this.listaAdicionaisSegundoSabor = this.page.locator('#lista-adicionais-segundo-sabor');
		this.valorTotalPersonalizacaoPizza = this.page.locator('#valor-total-personalizacao');
		this.botaoAdicionarPersonalizacaoPizza = this.page.locator('#botao-adicionar-personalizacao-pizza');
		this.valorTotalPedido = this.page.locator('#valor-total');
	}

	async abrir(): Promise<void> {
		await this.page.goto('/pedido/registrar');
	}

	async selecionarEtapaProdutos(): Promise<void> {
		await this.botaoEtapaProdutos.click();
	}

	async selecionarEtapaAtendimento(): Promise<void> {
		await this.botaoEtapaAtendimento.click();
	}

	async buscarPizza(filtro: string): Promise<void> {
		return this.buscarProduto('pizza', filtro);
	}

	async buscarBebida(filtro: string): Promise<void> {
		return this.buscarProduto('bebida', filtro);
	}

	async buscarSobremesa(filtro: string): Promise<void> {
		return this.buscarProduto('sobremesa', filtro);
	}

	async adicionarPizzaAoPedido(nome: string): Promise<void> {
		await this.adicionarProdutoAoPedido('pizza', nome);
	}

	async adicionarBebidaAoPedido(nome: string): Promise<void> {
		await this.adicionarProdutoAoPedido('bebida', nome);
	}

	async adicionarSobremesaAoPedido(nome: string): Promise<void> {
		await this.adicionarProdutoAoPedido('sobremesa', nome);
	}

	async selecionarTamanhoPizza(nome: string, tamanho: Tamanho): Promise<void> {
		await this.card('pizza', nome).getByTestId(`tamanho-pizza-${tamanho}`).click();
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

	async abrirModalPersonalizacaoPizza(nome: string): Promise<void> {
		await this.card('pizza', nome).locator('.personalizar-item').click();
	}

	async fecharModalPersonalizacaoPizza(): Promise<void> {
		await this.botaoFecharModalPersonalizacaoPizza.click();
	}

	async removerIngredientePersonalizavel(nomeIngrediente: string, sabor: Sabor = 1): Promise<void> {
		await this.ingredientePersonalizavel(nomeIngrediente, sabor).locator('.remover-ingrediente').click();
	}

	async aumentarQuantidadeIngredienteAdicional(nome: string, sabor: Sabor = 1): Promise<void> {
		await this.ingredienteAdicional(nome, sabor).locator('.aumentar-quantidade-adicional').click();
	}

	async diminuirQuantidadeIngredienteAdicional(nome: string, sabor: Sabor = 1): Promise<void> {
		await this.ingredienteAdicional(nome, sabor).locator('.diminuir-quantidade-adicional').click();
	}

	async adicionarPersonalizacaoAoPedido(): Promise<void> {
		await this.botaoAdicionarPersonalizacaoPizza.click();
	}

	async selecionarTipoAtendimento(tipoAtendimento: TipoAtendimento): Promise<void> {
		switch (tipoAtendimento) {
			case 'Entrega':
				await this.labelTipoAtendimentoEntrega.click();
				break;
			case 'Retirada':
				await this.labelTipoAtendimentoRetirada.click();
				break;
			case 'Balcão':
				await this.labelTipoAtendimentoBalcao.click();
				break;
		}
	}

	async selecionarTipoCliente(tipoCliente: TipoCliente): Promise<void> {
		switch (tipoCliente) {
			case 'Consumidor Final':
				await this.labelTipoClienteConsumidorFinal.click();
				break;
			case 'Cliente':
				await this.labelTipoClienteIdentificado.click();
				break;
		}
	}

	async buscarCliente(filtro: string): Promise<void> {
		await this.filtroCliente.fill(filtro);
		await this.botaoFiltrarCliente.click();
	}

	async selecionarCliente(nome: string): Promise<void> {
		await this.cliente(nome).click();
	}

	async registrarPedido(): Promise<void> {
		await this.botaoRegistrarPedido.click();
	}

	async deveAparecerCardDaPizza(nome: string): Promise<void> {
		const card = this.card('pizza', nome);
		const nomePizza = card.getByTestId('card-nome');
		const tamanhos = card.locator('.tamanhos-pizza');

		await expect(card).toHaveCount(1);
		await expect(nomePizza).toHaveText(nome);
		await expect(tamanhos).not.toHaveCount(0);
	}

	async deveAparecerMensagemNenhumaPizzaEncontrada(): Promise<void> {
		const mensagem = this.secaoPizza.locator('.mensagem');

		await expect(mensagem).toHaveCount(1);
		await expect(mensagem).toContainText('Nenhuma pizza encontrada.');
	}

	async deveAparecerCardDaBebida(nome: string): Promise<void> {
		const card = this.card('bebida', nome);
		const nomeBebida = card.getByTestId('card-nome');

		await expect(card).toHaveCount(1);
		await expect(nomeBebida).toHaveText(nome);
	}

	async deveAparecerMensagemNenhumaBebidaEncontrada(): Promise<void> {
		const mensagem = this.secaoBebida.locator('.mensagem');

		await expect(mensagem).toHaveCount(1);
		await expect(mensagem).toContainText('Nenhuma bebida encontrada.');
	}

	async deveAparecerTodasAsPizzasDoCardapio(): Promise<void> {
		const cards = this.secaoPizza.getByTestId('card-produto');

		await expect(cards).toHaveCount(10);
	}

	async deveAparecerTodasAsBebidasDoCardapio(): Promise<void> {
		const cards = this.secaoBebida.getByTestId('card-produto');

		await expect(cards).toHaveCount(3);
	}

	async deveAparecerProdutoNoResumo(nome: string, preco: string, descricao: string = '', quantidade: string = '1'): Promise<void> {
		const item = this.itemResumo(nome);
		const quantidadeItem = item.locator('.quantidade-item');
		const nomeItem = item.locator('.nome-item');
		const descricaoItem = item.locator('.descricao-item');
		const precoItem = item.locator('.preco-item');

		await expect(item).toHaveCount(1);
		await expect(nomeItem).toHaveText(nome);
		await expect(descricaoItem).toHaveText(descricao);
		await expect(precoItem).toHaveText(preco);
		await expect(quantidadeItem).toHaveValue(quantidade);
	}

	async deveExibirCarrinhoVazio(): Promise<void> {
		await expect(this.avisoCarrinhoVazio).toBeVisible();
		await expect(this.tituloCarrinho).toHaveText('Carrinho (0 itens)');
	}

	async deveExibirModalPersonalizacaoPizza(): Promise<void> {
		await expect(this.modalPersonalizacaoPizza).toBeVisible();
	}

	async deveNaoExibirModalPersonalizacaoPizza(): Promise<void> {
		await expect(this.modalPersonalizacaoPizza).not.toBeVisible();
	}

	async deveTerQuantidadeDeIngredientes(quantidade: number, sabor: Sabor = 1): Promise<void> {
		await expect(this.obterListaIngredientes(sabor).locator('li')).toHaveCount(quantidade);
	}

	async deveTerQuantidadeDeAdicionais(quantidade: number, sabor: Sabor = 1): Promise<void> {
		await expect(this.obterListaAdicionais(sabor).locator('li')).toHaveCount(quantidade);
	}

	async deveTerQuantidadeDeOpcoesDeIngredientesSubstitutos(quantidade: number, sabor: Sabor = 1): Promise<void> {
		await expect(this.obterOpcoesIngredientesSubstitutos(sabor).locator('option')).toHaveCount(quantidade);
	}

	async deveExibirValorTotalDaPersonalizacaoPizza(valor: string): Promise<void> {
		await expect(this.valorTotalPersonalizacaoPizza).toHaveText(valor);
	}

	async deveExibirValorTotalDoPedido(valor: string): Promise<void> {
		await expect(this.valorTotalPedido).toHaveText(valor);
	}

	async deveEstarMarcadoParaRemocaoOIngredientePersonalizavel(nome: string, sabor: Sabor = 1): Promise<void> {
		await expect(this.checkboxRemoverIngredientePersonalizavel(nome, sabor)).toBeChecked();
	}

	async deveTerFundoVermelhoOIngredientePersonalizavel(nome: string, sabor: Sabor = 1): Promise<void> {
		await expect(this.ingredientePersonalizavel(nome, sabor)).toContainClass('list-group-item-danger');
	}

	async deveEstarDesmarcadoParaRemocaoOIngredientePersonalizavel(nome: string, sabor: Sabor = 1): Promise<void> {
		await expect(this.checkboxRemoverIngredientePersonalizavel(nome, sabor)).not.toBeChecked();
	}

	async deveEstarDesabilitadoTipoClienteConsumidorFinal(): Promise<void> {
		await expect(this.labelTipoClienteConsumidorFinal).toBeDisabled();
	}

	async deveExibirPedidoSemClienteVinculado(): Promise<void> {
		await expect(this.secaoConsumidorFinal).toBeVisible();
		await expect(this.secaoConsumidorFinal).toContainText('Pedido sem cliente vinculado.');
	}

	async deveExibirSecaoDeClientes(): Promise<void> {
		await expect(this.secaoCliente).toBeVisible();
	}

	async deveExibirTaxaDeEntrega(): Promise<void> {
		await expect(this.secaoEntrega).toBeVisible();
		await expect(this.valorEntrega).toHaveText('R$ 10,00');
	}

	async deveExibirDescontoFidelidade(valor: string): Promise<void> {
		await expect(this.secaoFidelidade).toBeVisible();
		await expect(this.valorDescontoFidelidade).toHaveText(valor);
	}

	async deveNaoExibirDescontoFidelidade(): Promise<void> {
		await expect(this.secaoFidelidade).toBeHidden();
	}

	async deveExibirDescontoPromocao(valor: string): Promise<void> {
		await expect(this.secaoPromocoes).toBeVisible();
		await expect(this.valorDescontoPromocao).toHaveText(valor);
	}

	async deveNaoExibirDescontoPromocao(): Promise<void> {
		await expect(this.secaoPromocoes).toBeHidden();
	}

	async deveRedirecionarParaInicio(): Promise<void> {
		await expect(this.page).toHaveURL(/\/$/);
	}

	async deveExibirErroNoPedido(mensagem: string): Promise<void> {
		await expect(this.tituloCarrinho.locator('.mensagem')).toContainText(mensagem);
	}

	private secaoProdutos(categoria: Categoria): Locator {
		return this.page.locator(`#secao-${categoria}`);
	}

	private cards(categoria: Categoria): Locator {
		return this.secaoProdutos(categoria).getByTestId('card-produto');
	}

	private card(categoria: Categoria, nome: string): Locator {
		return this.cards(categoria).filter({ has: this.nomeExatoCard(nome) });
	}

	private nomeExatoCard(nome: string): Locator {
		return this.page.getByTestId('card-nome').getByText(nome, { exact: true });
	}

	private async buscarProduto(categoria: Categoria, filtro: string): Promise<void> {
		await this.page.locator(`label[for="categoria-${categoria}"]`).click();
		await this.filtroCardapio.fill(filtro);

		const respostaApi = this.aguardarBuscaProduto(categoria);
		await this.botaoFiltrarCardapio.click();
		await respostaApi;
	}

	private aguardarBuscaProduto(categoria: Categoria): Promise<Response> {
		const caminho = categoria === 'pizza' ? '/pizzas' : '/produtos-simples';

		return this.page.waitForResponse((resposta) => {
			return resposta.url().includes(caminho) && resposta.request().method() === 'GET' && resposta.ok();
		});
	}

	private async adicionarProdutoAoPedido(categoria: Categoria, nome: string): Promise<void> {
		await this.card(categoria, nome).locator('.adicionar-item').click();
	}

	private itemResumo(nome: string): Locator {
		return this.listaItensPedido.filter({ hasText: nome });
	}

	private cliente(identificador: string): Locator {
		return this.listaClientes.filter({ hasText: identificador });
	}

	private obterListaIngredientes(sabor: Sabor): Locator {
		if (sabor === 1) {
			return this.listaIngredientesPrimeiroSabor;
		}

		return this.listaIngredientesSegundoSabor;
	}

	private obterListaAdicionais(sabor: Sabor): Locator {
		if (sabor === 1) {
			return this.listaAdicionaisPrimeiroSabor;
		}

		return this.listaAdicionaisSegundoSabor;
	}

	private obterOpcoesIngredientesSubstitutos(sabor: Sabor): Locator {
		if (sabor === 1) {
			return this.listaIngredientesPrimeiroSabor.locator('.opcoes-troca');
		}

		return this.listaIngredientesSegundoSabor.locator('.opcoes-troca');
	}

	private ingredientePersonalizavel(nomeIngrediente: string, sabor: Sabor = 1): Locator {
		return this.obterListaIngredientes(sabor).locator(`.ingrediente-personalizavel`).filter({ hasText: nomeIngrediente });
	}

	private checkboxRemoverIngredientePersonalizavel(nomeIngrediente: string, sabor: Sabor = 1): Locator {
		return this.ingredientePersonalizavel(nomeIngrediente, sabor).locator('input[type="checkbox"]');
	}

	private ingredienteAdicional(nomeIngrediente: string, sabor: Sabor = 1): Locator {
		return this.obterListaAdicionais(sabor).locator(`.ingrediente-adicional`).filter({ hasText: nomeIngrediente });
	}
}
