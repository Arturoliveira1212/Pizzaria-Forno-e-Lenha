import { test } from '@playwright/test';
import { TelaRegistroPedido } from './pom/TelaRegistroPedido';

test.describe('Registro de pedido', () => {
	let tela: TelaRegistroPedido;

	test.beforeEach(async ({ page }) => {
		tela = new TelaRegistroPedido(page);
		await tela.abrir();
	});

	// Cenário: Busca de todas as pizzas no cardápio
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos" e filtro pela categoria "Pizzas"
	//     Então deve aparecer todas as 10 pizzas do cardápio
	test('Busca de todas as pizzas no cardápio', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('');
		await tela.deveAparecerTodasAsPizzasDoCardapio();
	});

	// Cenário: Busca de pizza existente no cardápio
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "Margherita"
	//     Então deve aparecer o card da pizza com nome, descrição, precos e tamanho
	test('Busca de pizza existente no cardápio', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('Margherita');
		await tela.deveAparecerCardDaPizza('Margherita');
	});

	// Cenário: Busca de bebida inexistente no cardápio
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "Pizza inexistente"
	//     Então deve aparecer a mensagem de aviso "Nenhuma pizza encontrada"
	test('Busca de pizza inexistente no cardápio', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('Pizza inexistente');
		await tela.deveAparecerMensagemNenhumaPizzaEncontrada();
	});

	//  Cenário: Busca de todas as bebidas no cardápio
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos" e filtro pela categoria "Bebidas"
	//     Então deve aparecer todas as 3 bebidas do cardápio
	test('Busca de todas as bebidas no cardápio', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarBebida('');
		await tela.deveAparecerTodasAsBebidasDoCardapio();
	});

	// Cenário: Busca de bebida existente no cardápio
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Bebidas" e por "Coca"
	//     Então deve aparecer o card do produto com nome, descrição e preço
	test('Busca de bebida existente no cardápio', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarBebida('Coca');
		await tela.deveAparecerCardDaBebida('Coca-Cola 2L');
	});

	// Cenário: Busca de bebida inexistente no cardápio
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Bebidas" e por "Bebida inexistente"
	//     Então deve aparecer a mensagem de aviso "Nenhuma bebida encontrada"
	test('Busca de bebida inexistente no cardápio', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarBebida('Bebida inexistente');
		await tela.deveAparecerMensagemNenhumaBebidaEncontrada();
	});

	// Cenário: Adição de pizza no pedido
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "2" e clico em adicionar
	//     Então deve aparecer no resumo lateral a pizza "Calabresa"
	test('Adição de pizza no pedido', async ({}) => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('2');
		await tela.deveAparecerCardDaPizza('Calabresa');
		await tela.adicionarPizzaAoPedido('Calabresa');
		await tela.deveAparecerPizzaNoResumo('Calabresa');
	});

	// Cenário: Adição de pizza com tamanho diferente do padrão
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "Catupiry" e seleciono o tamanho "Grande" e clico em adicionar
	//     Então deve aparecer no resumo lateral a pizza "Frango com Catupiry" no tamanho "Grande"
	test.only('Adição de pizza com tamanho diferente do padrão', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('Catupiry');
		await tela.selecionarTamanhoPizza('Frango com Catupiry', 'G');
		await tela.adicionarPizzaAoPedido('Frango com Catupiry');
		await tela.deveAparecerPizzaNoResumo('Frango com Catupiry', 'G');
	});

	// Cenário: Adição da mesma pizza duas vezes
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "8" e clico em adicionar 2 vezes
	//     Então deve aparecer no resumo lateral a pizza "Napolitana" com quantidade "2"
	test('Adição da mesma pizza duas vezes', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('8');
		await tela.adicionarPizzaAoPedido('Napolitana');
		await tela.adicionarPizzaAoPedido('Napolitana');
		await tela.deveTerQuantidadeNoResumo('Napolitana', '2');
	});

	// Cenário: Adição de sobremesa no pedido
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Sobremesas" e por "Pudim" e clico em adicionar
	//     Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite"
	test('Adição de sobremesa no pedido', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarSobremesa('Pudim');
		await tela.adicionarProdutoSimplesAoPedido('Pudim de Leite');
		await tela.deveAparecerProdutoNoResumo('Pudim de Leite');
	});

	// Cenário: Adição da mesma sobremesa duas vezes
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Produtos", filtro pela categoria "Sobremesas" e por "5" e clico em adicionar 2 vezes
	//     Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite" com quantidade "2"
	test('Adição da mesma sobremesa duas vezes', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarSobremesa('5');
		await tela.adicionarProdutoSimplesAoPedido('Pudim de Leite');
		await tela.adicionarProdutoSimplesAoPedido('Pudim de Leite');
		await tela.deveTerQuantidadeNoResumo('Pudim de Leite', '2');
	});

	// Cenário: Seleção de tipo atendimento Entrega
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Atendimento" e seleciono o tipo atendimento "Entrega"
	//     Então a opção de tipo cliente "Consumidor Final" deve ficar desabilitada.
	test('Seleção de tipo atendimento entrega', async () => {
		await tela.selecionarEtapaAtendimento();
		await tela.selecionarTipoAtendimento('Entrega');
		await tela.deveEstarDesabilitadoTipoClienteConsumidorFinal();
	});

	// Cenário: Seleção de tipo cliente Consumidor Final
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Atendimento" e seleciono o tipo cliente "Consumidor Final"
	//     Então a seção de clientes desaparece e aparece a frase "Pedido sem cliente vinculado."
	test('Seleção de tipo cliente Consumidor Final', async () => {
		await tela.selecionarEtapaAtendimento();
		await tela.selecionarTipoCliente('Consumidor Final');
		await tela.deveExibirPedidoSemClienteVinculado();
	});

	// Cenário: Seleção do tipo cliente Cliente Identificado
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Atendimento" e seleciono o tipo cliente "Cliente"
	//     Então a seção de clientes aparece.
	test('Seleção do tipo cliente Cliente Identificado', async () => {
		await tela.selecionarEtapaAtendimento();
		await tela.selecionarTipoCliente('Consumidor Final');
		await tela.selecionarTipoCliente('Cliente');
		await tela.deveExibirSecaoDeClientes();
	});

	// Cenário: Alteração de quantidade
	//     Dado que estou na tela de registro de pedido com a pizza "Calabresa" adicionada
	//     Quando aumento a quantidade para "2"
	//     Então o resumo deve ficar com "Calabresa" e quantidade "2"
	test('Alteração de quantidade', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('2');
		await tela.adicionarPizzaAoPedido('Calabresa');
		await tela.aumentarQuantidadeDoItem('Calabresa');
		await tela.deveTerQuantidadeNoResumo('Calabresa', '2');
	});

	// Cenário: Remoção de item no resumo
	//     Dado que estou na tela de registro de pedido com a pizza "Calabresa" adicionada
	//     Quando cliclo em remover o item
	//     Então o resumo deve ficar vazio
	test('Remoção de item no resumo', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('2');
		await tela.adicionarPizzaAoPedido('Calabresa');
		await tela.removerItemDoResumo('Calabresa');
		await tela.deveExibirCarrinhoVazio();
	});

	// Cenário: Tentativa de finalizar pedido sem itens
	//     Dado que estou na tela de registro de pedido
	//     Quando acesso a etapa "Atendimento" e clico em "Registrar pedido"
	//     Então deve aparecer a mensagem de erro "Não é possível registrar um pedido sem itens."
	test('Tentativa de finalizar pedido sem itens', async () => {
		await tela.selecionarEtapaAtendimento();
		await tela.registrarPedido();
		await tela.deveExibirErroNoPedido('Não é possível registrar um pedido sem itens.');
	});

	// Cenário: Aplicação da taxa de entrega com produtos no pedido
	//     Dado que estou na tela de registro de pedido com a pizza "Calabresa" adicionada
	//     Quando acesso a etapa "Atendimento" e seleciono o tipo atendimento "Entrega"
	//     Então o resumo deve exibir a taxa de entrega de "R$ 10,00"
	test('Aplicação da taxa de entrega com produtos no pedido', async () => {
		await tela.selecionarEtapaProdutos();
		await tela.buscarPizza('2');
		await tela.adicionarPizzaAoPedido('Calabresa');
		await tela.selecionarEtapaAtendimento();
		await tela.buscarCliente('Ana Beatriz Souza');
		await tela.selecionarCliente('Ana Beatriz Souza');
		await tela.selecionarTipoAtendimento('Entrega');
		await tela.deveExibirTaxaDeEntrega();
	});
});
