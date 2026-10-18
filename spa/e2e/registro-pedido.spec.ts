import { test } from '@playwright/test';
import { TelaRegistroPedido } from './pom/TelaRegistroPedido';

test.describe('Registro de pedido', () => {
	let tela: TelaRegistroPedido;

	test.beforeEach(async ({ page }) => {
		tela = new TelaRegistroPedido(page);
		await tela.abrir();
	});

	/*
		Funcionalidade: Buscar pizzas no cardápio
			A fim de encontrar rapidamente a pizza desejada pelo cliente
			Como um atendente
			Eu quero filtrar e pesquisar pizzas no cardápio
			De modo a adicioná-las ao pedido
	*/
	test.describe('Buscar pizzas no cardápio', () => {
		test.describe('Dado que estou na tela de registro de pedido E acesso a etapa "Produtos"', () => {
			test.beforeEach(async () => {
				await tela.selecionarEtapaProdutos();
			});

			/*
				Cenário: Busca de todas as pizzas no cardápio
					Quando filtro pela categoria "Pizzas"
					Então deve aparecer todas as 10 pizzas do cardápio
			*/
			test('Busca de todas as pizzas no cardápio', async ({}) => {
				await tela.buscarPizza('');
				await tela.deveAparecerTodasAsPizzasDoCardapio();
			});

			/*
				Cenário: Busca de pizza existente no cardápio
					Quando filtro pela categoria "Pizzas" e pesquiso por "Quatro Queijos"
					Então deve aparecer o card da pizza "Quatro Queijos" com nome, descrição, preços e tamanho
			*/
			test('Busca de pizza existente no cardápio', async ({}) => {
				await tela.buscarPizza('Quatro Queijos');
				await tela.deveAparecerCardDaPizza('Quatro Queijos');
			});

			/*
				Cenário: Busca de pizza inexistente no cardápio
					Quando filtro pela categoria "Pizzas" e pesquiso por "Pizza inexistente"
					Então deve aparecer a mensagem de aviso "Nenhuma pizza encontrada"
			*/
			test('Busca de pizza inexistente no cardápio', async ({}) => {
				await tela.buscarPizza('Pizza inexistente');
				await tela.deveAparecerMensagemNenhumaPizzaEncontrada();
			});
		});
	});

	/*
		Funcionalidade: Buscar bebidas no cardápio
			A fim de encontrar rapidamente a bebida desejada pelo cliente
			Como um atendente
			Eu quero filtrar e pesquisar bebidas no cardápio
			De modo a adicioná-las ao pedido
	*/
	test.describe('Buscar bebidas no cardápio', () => {
		test.describe('Dado que estou na tela de registro de pedido E acesso a etapa "Produtos"', () => {
			test.beforeEach(async () => {
				await tela.selecionarEtapaProdutos();
			});

			/*
				Cenário: Busca de todas as bebidas no cardápio
					Quando filtro pela categoria "Bebidas"
					Então deve aparecer todas as 3 bebidas do cardápio
			*/
			test('Busca de todas as bebidas no cardápio', async ({}) => {
				await tela.buscarBebida('');
				await tela.deveAparecerTodasAsBebidasDoCardapio();
			});

			/*
				Cenário: Busca de bebida existente no cardápio
					Quando filtro pela categoria "Bebidas" e pesquiso por "Coca"
					Então deve aparecer o card da bebida "Coca-Cola 2L" com nome, descrição e preço
			*/
			test('Busca de bebida existente no cardápio', async ({}) => {
				await tela.buscarBebida('Coca');
				await tela.deveAparecerCardDaBebida('Coca-Cola 2L');
			});

			/*	Cenário: Busca de bebida inexistente no cardápio
					Quando filtro pela categoria "Bebidas" e pesquiso por "Bebida inexistente"
					Então deve aparecer a mensagem de aviso "Nenhuma bebida encontrada"
			*/
			test('Busca de bebida inexistente no cardápio', async ({}) => {
				await tela.buscarBebida('Bebida inexistente');
				await tela.deveAparecerMensagemNenhumaBebidaEncontrada();
			});
		});
	});

	/*
		Funcionalidade: Gerenciar itens do pedido
			A fim de montar o pedido do cliente sem erros
			Como um atendente
			Eu quero adicionar, alterar e remover pizzas e sobremesas no pedido
			De modo a enviar o pedido correto para a cozinha
	*/
	test.describe('Gerenciar itens no pedido', () => {
		test.describe('Dado que estou na tela de registro de pedido E acesso a etapa "Produtos"', () => {
			test.beforeEach(async () => {
				await tela.selecionarEtapaProdutos();
			});

			/*
				Cenário: Adição de pizza no pedido
					Quando filtro pela categoria "Pizzas" e pesquiso por ID "2"
					E clico em adicionar a pizza "Calabresa"
					Então deve aparecer no resumo lateral a pizza "Calabresa"
			*/
			test('Adição de pizza no pedido', async ({}) => {
				await tela.buscarPizza('2');
				await tela.adicionarPizzaAoPedido('Calabresa');
				await tela.deveAparecerProdutoNoResumo('Pizza P', 'Calabresa');
			});

			/*	Cenário: Adição de pizza com tamanho diferente do padrão
					Quando filtro pela categoria "Pizzas" e pesquiso por "Catupiry"
					E seleciono o tamanho "Grande" para a pizza "Frango com Catupiry"
					E clico em adicionar a pizza "Frango com Catupiry"
					Então deve aparecer no resumo lateral a pizza "Frango com Catupiry" no tamanho "Grande"
			*/
			test('Adição de pizza com tamanho diferente do padrão', async () => {
				await tela.buscarPizza('Catupiry');
				await tela.selecionarTamanhoPizza('Frango com Catupiry', 'G');
				await tela.adicionarPizzaAoPedido('Frango com Catupiry');
				await tela.deveAparecerProdutoNoResumo('Pizza G', 'Frango com Catupiry');
			});

			/*	Cenário: Adição da mesma pizza duas vezes
					Quando filtro pela categoria "Pizzas" e pesquiso por ID "8"
					E clico em adicionar a pizza "Napolitana" 2 vezes
					Então deve aparecer no resumo lateral a pizza "Napolitana" com quantidade "2"
			*/
			test('Adição da mesma pizza duas vezes', async () => {
				await tela.buscarPizza('8');
				await tela.adicionarPizzaAoPedido('Napolitana');
				await tela.adicionarPizzaAoPedido('Napolitana');
				await tela.deveAparecerProdutoNoResumo('Pizza P', 'Napolitana', '2');
			});

			/*	Cenário: Adição de sobremesa no pedido
					Quando filtro pela categoria "Sobremesas" e pesquiso por "Pudim"
					E clico em adicionar a sobremesa "Pudim de Leite"
					Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite"
			*/
			test('Adição de sobremesa no pedido', async () => {
				await tela.buscarSobremesa('Pudim');
				await tela.adicionarSobremesaAoPedido('Pudim de Leite');
				await tela.deveAparecerProdutoNoResumo('Pudim de Leite');
			});

			/*	Cenário: Adição da mesma sobremesa duas vezes
					Quando filtro pela categoria "Sobremesas" e pesquiso por ID "5"
					E clico em adicionar a sobremesa "Pudim de Leite" 2 vezes
					Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite" com quantidade "2"
			*/
			test('Adição da mesma sobremesa duas vezes', async () => {
				await tela.buscarSobremesa('5');
				await tela.adicionarSobremesaAoPedido('Pudim de Leite');
				await tela.adicionarSobremesaAoPedido('Pudim de Leite');
				await tela.deveAparecerProdutoNoResumo('Pudim de Leite', '', '2');
			});

			/*
				Cenário: Aumento da quantidade de um item
					Dado que a pizza "Calabresa" foi adicionada ao pedido 1 vez
					Quando aumento a quantidade de "Calabresa" para "2"
					Então o resumo deve ficar com "Calabresa" e quantidade "2"
			*/
			test('Aumento da quantidade de um item', async () => {
				await tela.buscarPizza('2');
				await tela.adicionarPizzaAoPedido('Calabresa');
				await tela.aumentarQuantidadeDoItem('Calabresa');
				await tela.deveAparecerProdutoNoResumo('Pizza P', 'Calabresa', '2');
			});

			/*
				Cenário: Diminuição da quantidade de um item
					Dado que a pizza "Portuguesa" foi adicionada ao pedido 3 vezes
					Quando diminuo a quantidade de "Portuguesa" para "2"
					Então o resumo deve ficar com "Portuguesa" e quantidade "2"
			*/
			test('Diminuição da quantidade de um item', async () => {
				await tela.buscarPizza('Portuguesa');
				await tela.adicionarPizzaAoPedido('Portuguesa');
				await tela.adicionarPizzaAoPedido('Portuguesa');
				await tela.adicionarPizzaAoPedido('Portuguesa');
				await tela.diminuirQuantidadeDoItem('Portuguesa');
				await tela.deveAparecerProdutoNoResumo('Pizza P', 'Portuguesa', '2');
			});

			/*
				Cenário: Remoção de item no resumo
					Dado que a pizza "Calabresa" foi adicionada ao pedido
					Quando clico em remover o item "Calabresa"
					Então o resumo deve ficar vazio
			*/
			test('Remoção de item no resumo', async () => {
				await tela.buscarPizza('2');
				await tela.adicionarPizzaAoPedido('Calabresa');
				await tela.removerItemDoResumo('Calabresa');
				await tela.deveExibirCarrinhoVazio();
			});
		});
	});

	/*
		Funcionalidade: Definir atendimento e cliente
			A fim de registrar corretamente como o pedido será atendido e para quem
			Como um atendente
			Eu quero escolher o tipo de atendimento e o tipo de cliente
			De modo a vincular o pedido às informações corretas
	*/
	test.describe('Definir atendimento e cliente', () => {
		test.describe('Dado que estou na tela de registro de pedido E acesso a etapa "Atendimento"', () => {
			test.beforeEach(async () => {
				await tela.selecionarEtapaAtendimento();
			});

			/*
				Cenário: Seleção de tipo de atendimento Entrega
					Quando seleciono o tipo de atendimento "Entrega"
					Então a opção de tipo de cliente "Consumidor Final" deve ficar desabilitada
			*/
			test('Seleção de tipo atendimento Entrega', async () => {
				await tela.selecionarTipoAtendimento('Entrega');
				await tela.deveEstarDesabilitadoTipoClienteConsumidorFinal();
			});

			/*
				Cenário: Seleção de tipo de cliente Consumidor Final
					Quando seleciono o tipo de cliente "Consumidor Final"
					Então a seção de clientes deve desaparecer
					E deve aparecer a frase "Pedido sem cliente vinculado."
			*/
			test('Seleção de tipo cliente Consumidor Final', async () => {
				await tela.selecionarTipoCliente('Consumidor Final');
				await tela.deveExibirPedidoSemClienteVinculado();
			});

			/*
				Cenário: Seleção de tipo de cliente Cliente Identificado
					Dado que selecionei o tipo de cliente "Consumidor Final"
					Quando seleciono o tipo de cliente "Cliente"
					Então a seção de clientes deve aparecer
			*/
			test('Seleção do tipo cliente Cliente Identificado', async () => {
				await tela.selecionarTipoCliente('Consumidor Final');
				await tela.selecionarTipoCliente('Cliente');
				await tela.deveExibirSecaoDeClientes();
			});
		});
	});

	/*
		Funcionalidade: Aplicar taxas e descontos no pedido
			A fim de cobrar o valor correto do cliente
			Como um atendente
			Eu quero que taxas e descontos sejam aplicados automaticamente ao pedido
			De modo a evitar erros de cálculo no resumo
	*/
	test.describe('Aplicação de taxas e descontos', () => {
		test.describe('Dado que estou na tela de registro de pedido', () => {
			/*
				Cenário: Aplicação da taxa de entrega com produtos no pedido
					Dado que a pizza "Calabresa" foi adicionada ao pedido
					E acesso a etapa "Atendimento"
					E vinculo o cliente "Ana Beatriz Souza"
					Quando seleciono o tipo de atendimento "Entrega"
					Então o resumo deve exibir a taxa de entrega de "R$ 10,00"
			*/
			test('Aplicação da taxa de entrega com produtos no pedido', async () => {
				await tela.selecionarEtapaProdutos();
				await tela.buscarPizza('2');
				await tela.adicionarPizzaAoPedido('Calabresa');
				await tela.selecionarEtapaAtendimento();
				await tela.selecionarTipoAtendimento('Entrega');
				await tela.buscarCliente('Ana Beatriz Souza');
				await tela.selecionarCliente('Ana Beatriz Souza');
				await tela.deveExibirTaxaDeEntrega();
			});

			/*
				Cenário: Aplicação do desconto de fidelidade para cliente elegível
					Dado que o cliente "Artur Alves" possui um benefício de fidelidade
					E a pizza "Frango com Catupiry" foi adicionada ao pedido
					Quando acesso a etapa "Atendimento"
					E vinculo o cliente "Artur Alves"
					Então o resumo deve exibir o desconto de fidelidade
			*/
			test('Aplicação do desconto de fidelidade para cliente elegível', async () => {
				await tela.selecionarEtapaProdutos();
				await tela.buscarPizza('Frango com Catupiry');
				await tela.adicionarPizzaAoPedido('Frango com Catupiry');
				await tela.selecionarEtapaAtendimento();
				await tela.buscarCliente('Artur Alves');
				await tela.selecionarCliente('Artur Alves');
				await tela.deveExibirDescontoFidelidade();
			});

			/*
				Cenário: Não aplicação do desconto de fidelidade para cliente não elegível
					Dado que o cliente "Gabriel Santos Rocha" não possui um benefício de fidelidade
					E a pizza "Pepperoni" foi adicionada ao pedido
					Quando acesso a etapa "Atendimento"
					E vinculo o cliente "Gabriel Santos Rocha"
					Então o resumo não deve exibir o desconto de fidelidade
			*/
			test('Não aplicação do desconto de filidade para cliente não elegível', async () => {
				await tela.selecionarEtapaProdutos();
				await tela.buscarPizza('Pepperoni');
				await tela.adicionarPizzaAoPedido('Pepperoni');
				await tela.selecionarEtapaAtendimento();
				await tela.buscarCliente('Gabriel Santos Rocha');
				await tela.selecionarCliente('Gabriel Santos Rocha');
				await tela.deveNaoExibirDescontoFidelidade();
			});

			/*
				Cenário: Aplicação do desconto de promoção para pizzas elegíveis
					Dado que existe uma promoção vigente para pizzas "Grandes" a partir de 2 unidades
					Quando adiciono 2 pizzas "Frango com Catupiry" no tamanho "Grande"
					Então o resumo deve exibir o desconto de promoção
			*/
			test('Aplicação do desconto de promoção para pizzas elegíveis', async () => {
				await tela.selecionarEtapaProdutos();
				await tela.buscarPizza('Frango com Catupiry');
				await tela.selecionarTamanhoPizza('Frango com Catupiry', 'G');
				await tela.adicionarPizzaAoPedido('Frango com Catupiry');
				await tela.adicionarPizzaAoPedido('Frango com Catupiry');
				await tela.deveExibirDescontoPromocao();
			});

			/*
				Cenário: Não aplicação do desconto de promoção para pizzas não elegíveis
					Dado que existe uma promoção vigente para pizzas "Grandes" a partir de 2 unidades
					Quando adiciono 1 pizza "Vegetariana" no tamanho "Grande"
					Então o resumo não deve exibir o desconto de promoção
			*/
			test('Não aplicação do desconto de promoção para pizzas não elegíveis', async () => {
				await tela.selecionarEtapaProdutos();
				await tela.buscarPizza('Vegetariana');
				await tela.selecionarTamanhoPizza('Vegetariana', 'G');
				await tela.adicionarPizzaAoPedido('Vegetariana');
				await tela.deveNaoExibirDescontoPromocao();
			});
		});
	});

	/*
		Funcionalidade: Finalizar pedido
			A fim de enviar o pedido para a cozinha
			Como um atendente
			Eu quero registrar o pedido apenas quando ele estiver válido
			De modo a evitar pedidos vazios ou incorretos
	*/
	test.describe('Finalização de pedido', () => {
		test.describe('Dado que estou na tela de registro de pedido', () => {
			/*
				Cenário: Tentativa de finalizar pedido sem itens
					Dado que acesso a etapa "Atendimento"
					Quando clico em "Registrar pedido"
					Então deve aparecer a mensagem de erro "Não é possível registrar um pedido sem itens."
			*/
			test('Tentativa de finalizar pedido sem itens', async () => {
				await tela.selecionarEtapaAtendimento();
				await tela.registrarPedido();
				await tela.deveExibirErroNoPedido('Não é possível registrar um pedido sem itens.');
			});

			/*
				Cenário: Finalização de pedido com sucesso
					Dado que a pizza "Bacon" foi adicionada ao pedido
					E acesso a etapa "Atendimento"
					E vinculo o cliente "Artur Alves"
					Quando clico em "Registrar pedido"
					Então deve aparecer a mensagem de pedido realizado
					E devo ser redirecionado para a home
			*/
			test('Finalização de pedido com sucesso', async () => {
				await tela.selecionarEtapaProdutos();
				await tela.buscarPizza('Bacon');
				await tela.adicionarPizzaAoPedido('Bacon');
				await tela.selecionarEtapaAtendimento();
				await tela.buscarCliente('Artur Alves');
				await tela.selecionarCliente('Artur Alves');
				await tela.registrarPedido();
				await tela.deveRedirecionarParaInicio();
			});
		});
	});
});
