import { APIRequestContext, test } from '@playwright/test';
import { TelaListagemPedidos } from './pom/TelaListagemPedidos';

const URL_API = 'http://localhost:8081';
const ID_AGUA_MINERAL = 3;

async function registrarPedido(request: APIRequestContext, tipoAtendimento: string, idCliente: string = ''): Promise<number> {
	await request.post(`${URL_API}/pedidos`, {
		data: {
			tipoAtendimento,
			idCliente,
			itensPedido: [{ idProduto: ID_AGUA_MINERAL, quantidade: 1 }],
		},
	});

	const resposta = await request.get(`${URL_API}/pedidos`);
	const corpo = await resposta.json();

	return corpo.data.pedidos[0].id;
}

async function alterarStatusDoPedido(request: APIRequestContext, idPedido: number, status: string): Promise<void> {
	await request.patch(`${URL_API}/pedidos/status`, { data: { idPedido, status } });
}

test.describe('Listagem de pedidos', () => {
	let tela: TelaListagemPedidos;
	let idPedido: number;

	test.beforeEach(async ({ page }) => {
		tela = new TelaListagemPedidos(page);
	});

	/*
		Funcionalidade: Registrar saída do pedido para entrega
			A fim de saber quem levou cada pedido e quando ele saiu
			Como um atendente
			Eu quero informar o entregador ao alterar o status do pedido para "SAIU_PARA_ENTREGA"
			De modo a acompanhar a entrega do pedido até o cliente
	*/
	test.describe('Registrar saída do pedido para entrega', () => {
		test.describe('Dado que existe um pedido de entrega com status "EM_PREPARACAO" E estou na tela de listagem de pedidos', () => {
			test.beforeEach(async ({ page }) => {
				idPedido = await registrarPedido(page.request, 'ENTREGA', '1');
				await alterarStatusDoPedido(page.request, idPedido, 'EM_PREPARACAO');
				await tela.abrir();
			});

			/*
				Cenário: Saída para entrega com entregador disponível
					Quando altero o status do pedido para "SAIU_PARA_ENTREGA"
					Então devem aparecer para seleção apenas os entregadores disponíveis "João Pedro Alves" e "Marcos Vinícius Rocha"
					Quando seleciono o entregador "João Pedro Alves" e registro a saída
					Então o pedido deve aparecer com o status "SAIU_PARA_ENTREGA"
			*/
			test('Saída para entrega com entregador disponível', async () => {
				await tela.alterarStatusDoPedido(idPedido, 'SAIU_PARA_ENTREGA');
				await tela.deveAparecerApenasOsEntregadores(['João Pedro Alves', 'Marcos Vinícius Rocha']);
				await tela.selecionarEntregadorERegistrarSaida('João Pedro Alves');
				await tela.deveExibirStatusDoPedido(idPedido, 'SAIU_PARA_ENTREGA');
			});
		});
	});

	/*
		Funcionalidade: Cancelar pedido
			A fim de manter o histórico dos pedidos que não foram concluídos
			Como um atendente
			Eu quero cancelar o pedido informando o motivo
			De modo a registrar por que o pedido não foi concluído
	*/
	test.describe('Cancelar pedido', () => {
		test.describe('Dado que existe um pedido de balcão com status "RECEBIDO" E estou na tela de listagem de pedidos', () => {
			test.beforeEach(async ({ page }) => {
				idPedido = await registrarPedido(page.request, 'BALCAO');
				await tela.abrir();
			});

			/*
				Cenário: Cancelamento de pedido com motivo
					Quando altero o status do pedido para "CANCELADO"
					E informo o motivo "Cliente desistiu do pedido" e confirmo o cancelamento
					Então o pedido deve aparecer com o status "CANCELADO"
			*/
			test('Cancelamento de pedido com motivo', async () => {
				await tela.alterarStatusDoPedido(idPedido, 'CANCELADO');
				await tela.informarMotivoEConfirmarCancelamento('Cliente desistiu do pedido');
				await tela.deveExibirStatusDoPedido(idPedido, 'CANCELADO');
			});

			/*
				Cenário: Tentativa de cancelamento de pedido sem motivo
					Quando altero o status do pedido para "CANCELADO"
					E confirmo o cancelamento sem informar o motivo
					Então o modal de cancelamento deve continuar aberto exigindo o motivo
			*/
			test('Tentativa de cancelamento de pedido sem motivo', async () => {
				await tela.alterarStatusDoPedido(idPedido, 'CANCELADO');
				await tela.confirmarCancelamentoSemMotivo();
				await tela.deveExibirModalCancelamentoExigindoMotivo();
			});
		});
	});
});
