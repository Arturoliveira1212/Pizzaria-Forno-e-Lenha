import { beforeEach, describe, expect, it } from 'vitest';
import { Cliente } from '../src/cliente/dominio/cliente';
import { GestorPedido } from '../src/pedido/dominio/gestor-pedido';
import { ItemPizza } from '../src/pedido/dominio/item-pizza';
import { ItemSimples } from '../src/pedido/dominio/item-simples';
import { TipoAtendimento } from '../src/pedido/dominio/tipo-atendimento';
import { RepositorioPedido } from '../src/pedido/repositorio/repositorio-pedido';
import { ResumoValoresPedido } from '../src/pedido/dominio/resumo-valores-pedido';
import { Pedido } from '../src/pedido/dominio/pedido';
import { DominioError } from '../src/infra/exceptions/dominio-error';

export class RepositorioPedidoEmMemoria implements RepositorioPedido {
	public pedidosRegistrados: Pedido[] = [];

	async obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido> {
		const subtotal = pedido.itensPedido.reduce((total, item) => total + item.preco * item.quantidade, 0);
		const taxaEntrega = pedido.tipoAtendimento === TipoAtendimento.ENTREGA ? 10 : 0;

		return new ResumoValoresPedido(0, 0, 0, taxaEntrega, subtotal + taxaEntrega);
	}

	async registrarPedido(pedido: Pedido): Promise<void> {
		this.pedidosRegistrados.push(pedido);
	}
}

describe('GestorPedido', () => {
	let repositorio: RepositorioPedidoEmMemoria;
	let gestor: GestorPedido;

	beforeEach(() => {
		repositorio = new RepositorioPedidoEmMemoria();
		gestor = new GestorPedido(repositorio);
	});

	describe('itens simples', () => {
		it('adiciona um item e atualiza o valor total', async () => {
			const pedido = await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			expect(pedido.itensPedido).toEqual([new ItemSimples(1, 10, 'Refrigerante', 6)]);
			expect(pedido.valorTotal).toBe(6);
		});

		it('junta itens iguais somando as quantidades', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});
			const pedido = await gestor.adicionarItemSimples({
				quantidade: 2,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			expect(pedido.itensPedido).toHaveLength(1);
			expect(pedido.itensPedido[0]).toEqual(new ItemSimples(3, 10, 'Refrigerante', 6));
			expect(pedido.valorTotal).toBe(18);
		});

		it('mantém separados itens de produtos diferentes', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});
			const pedido = await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 11,
				nome: 'Suco',
				preco: 8,
			});

			expect(pedido.itensPedido).toHaveLength(2);
			expect(pedido.valorTotal).toBe(14);
		});
	});

	describe('itens pizza', () => {
		it('adiciona uma pizza e atualiza o valor total', async () => {
			const pedido = await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});

			expect(pedido.itensPedido).toHaveLength(1);
			expect(pedido.itensPedido[0]).toBeInstanceOf(ItemPizza);
			expect(pedido.valorTotal).toBe(40);
		});

		it('junta pizzas com mesmo tamanho e sabores', async () => {
			await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});
			const pedido = await gestor.adicionarItemPizza({
				quantidade: 2,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});

			expect(pedido.itensPedido).toHaveLength(1);
			expect(pedido.itensPedido[0].quantidade).toBe(3);
			expect(pedido.valorTotal).toBe(120);
		});

		it('mantém separadas pizzas de tamanhos diferentes', async () => {
			await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});
			const pedido = await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'GRANDE',
				preco: 55,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});

			expect(pedido.itensPedido).toHaveLength(2);
			expect(pedido.valorTotal).toBe(95);
		});

		it('mantém separadas pizzas com sabores diferentes', async () => {
			await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});
			const pedido = await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 2, nome: 'Calabresa' }],
			});

			expect(pedido.itensPedido).toHaveLength(2);
			expect(pedido.valorTotal).toBe(80);
		});

		it('mantém separados uma pizza e um item simples', async () => {
			await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});
			const pedido = await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			expect(pedido.itensPedido).toHaveLength(2);
			expect(pedido.valorTotal).toBe(46);
		});
	});

	describe('quantidade e remoção de itens', () => {
		it('lança exceção ao tentar alterar a quantidade de um item inexistente', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const tentativa = gestor.alterarQuantidade(1, 1);

			await expect(tentativa).rejects.toBeInstanceOf(DominioError);
			await expect(tentativa).rejects.toThrow('Item do pedido não encontrado.');
		});

		it('aumenta a quantidade com delta positivo', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const pedido = await gestor.alterarQuantidade(0, 2);

			expect(pedido.itensPedido[0].quantidade).toBe(3);
			expect(pedido.valorTotal).toBe(18);
		});

		it('diminui a quantidade sem remover o item enquanto ela for maior que zero', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 3,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const pedido = await gestor.alterarQuantidade(0, -1);

			expect(pedido.itensPedido[0].quantidade).toBe(2);
			expect(pedido.valorTotal).toBe(12);
		});

		it('lança exceção ao remover item não existente', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const tentativa = gestor.removerItem(1);

			await expect(tentativa).rejects.toBeInstanceOf(DominioError);
			await expect(tentativa).rejects.toThrow('Item do pedido não encontrado.');
		});

		it('remove o item quando a quantidade chega a zero', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 2,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const pedido = await gestor.alterarQuantidade(0, -2);

			expect(pedido.itensPedido).toEqual([]);
			expect(pedido.valorTotal).toBe(0);
		});

		it('remove apenas o item do índice informado', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 11,
				nome: 'Suco',
				preco: 8,
			});

			const pedido = await gestor.removerItem(0);

			expect(pedido.itensPedido).toEqual([new ItemSimples(1, 11, 'Suco', 8)]);
			expect(pedido.valorTotal).toBe(8);
		});

		it('zera os valores ao remover o último item', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const pedido = await gestor.removerItem(0);

			expect(pedido.estaVazio()).toBe(true);
			expect(pedido.valorTotal).toBe(0);
		});
	});

	describe('cliente', () => {
		it('adiciona cliente ao pedido', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const comCliente = await gestor.adicionarCliente('cliente-1');
			expect(comCliente.cliente).toEqual(new Cliente('cliente-1'));
		});

		it('remove cliente do pedido', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});
			await gestor.adicionarCliente('cliente-1');

			const semCliente = await gestor.removerCliente();
			expect(semCliente.cliente).toBeUndefined();
		});
	});

	describe('tipo de atendimento', () => {
		it('altera tipo de atendimento do pedido', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			const comEntrega = await gestor.atualizarTipoAtendimento(TipoAtendimento.ENTREGA);
			expect(comEntrega.tipoAtendimento).toBe(TipoAtendimento.ENTREGA);
			expect(comEntrega.taxaEntrega).toBe(10);
			expect(comEntrega.valorTotal).toBe(16);
		});
	});

	describe('registro do pedido', () => {
		it('registra o pedido no repositório (itens simples)', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			await gestor.registrarPedido();

			expect(repositorio.pedidosRegistrados).toHaveLength(1);
			expect(repositorio.pedidosRegistrados[0].itensPedido).toHaveLength(1);
			expect(repositorio.pedidosRegistrados[0].valorTotal).toBe(6);
		});

		it('registra o pedido no repositório (itens pizza)', async () => {
			await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});

			await gestor.registrarPedido();

			expect(repositorio.pedidosRegistrados).toHaveLength(1);
			expect(repositorio.pedidosRegistrados[0].itensPedido).toHaveLength(1);
			expect(repositorio.pedidosRegistrados[0].valorTotal).toBe(40);
		});

		it('registra o pedido no repositório (itens mistos)', async () => {
			await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			await gestor.registrarPedido();

			expect(repositorio.pedidosRegistrados).toHaveLength(1);
			expect(repositorio.pedidosRegistrados[0].itensPedido).toHaveLength(2);
			expect(repositorio.pedidosRegistrados[0].valorTotal).toBe(46);
		});
	});
});
