import { beforeEach, describe, expect, it } from 'vitest';
import { Cliente } from '../src/cliente/dominio/cliente';
import { ItemPizza } from '../src/pedido/dominio/item-pizza';
import { ItemSimples } from '../src/pedido/dominio/item-simples';
import { TipoAtendimento } from '../src/pedido/dominio/tipo-atendimento';
import { RepositorioPedido } from '../src/pedido/repositorio/repositorio-pedido';
import { ResumoValoresPedido } from '../src/pedido/dominio/resumo-valores-pedido';
import { Pedido } from '../src/pedido/dominio/pedido';
import { DominioError } from '../src/infra/exceptions/dominio-error';
import { PedidoListagem } from '../src/pedido/dominio/pedido-listagem';
import { GestorRegistroPedido } from '../src/pedido/dominio/gestor-registro-pedido';
import { Pizza } from '../src/pizza/dominio/pizza';
import { RepositorioPizza } from '../src/pizza/repositorio/repositorio-pizza';
import { ProdutoSimples } from '../src/produto-simples/dominio/produto-simples';
import { TipoProdutoSimples } from '../src/produto-simples/dominio/tipo-produto-simples';
import { RepositorioProdutoSimples } from '../src/produto-simples/repositorio/repositorio-produto-simples';
import { RepositorioCliente } from '../src/cliente/repositorio/repositorio-cliente';
import { TamanhoPizza } from '../src/pizza/dominio/tamanho-pizza';
import { SaborEscolhido } from '../src/pedido/dominio/sabor-escolhido';

export class RepositorioPedidoEmMemoria implements RepositorioPedido {
	async obterPedidos(identificador?: string): Promise<PedidoListagem[]> {
		return [new PedidoListagem(1, '2024-06-01', 'Cliente 1', 'BALCAO', 'PENDENTE', ['EM_PREPARO'], 50)];
	}

	async alterarStatus(idPedido: number, status: string): Promise<void> {}

	async obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido> {
		return new ResumoValoresPedido(0, 0, 0, 0, 150);
	}

	async registrarPedido(pedido: Pedido): Promise<void> {}
}

export class RepositorioPizzaEmMemoria implements RepositorioPizza {
	async obterPizzasParaCardapio(identificador: string): Promise<Pizza[]> {
		return [new Pizza(1, 'Margherita'), new Pizza(2, 'Calabresa')];
	}
}

export class RepositorioProdutoSimplesEmMemoria implements RepositorioProdutoSimples {
	async obterProdutosSimples(tipo: TipoProdutoSimples, identificador: string): Promise<ProdutoSimples[]> {
		return [new ProdutoSimples(10, 'Refrigerante', 'Refrigerante gelado', TipoProdutoSimples.BEBIDA, 6)];
	}
}

export class RepositorioClienteEmMemoria implements RepositorioCliente {
	async obterClientes(identificador: string): Promise<Cliente[]> {
		return [new Cliente(1, 'Cliente 1', '123456789', '123.456.789-00')];
	}
}

describe('GestorPedido', () => {
	let gestor: GestorRegistroPedido;

	beforeEach(() => {
		gestor = new GestorRegistroPedido(new RepositorioPedidoEmMemoria(), new RepositorioPizzaEmMemoria(), new RepositorioProdutoSimplesEmMemoria(), new RepositorioClienteEmMemoria());
	});

	describe('itens simples', () => {
		it('adiciona um item corretamente ao pedido', async () => {
			const pedido = await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});

			expect(pedido.itensPedido).toEqual([new ItemSimples(1, 10, 'Refrigerante', 6)]);
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
			expect(pedido.itensPedido[0]).toEqual(new ItemSimples(1, 10, 'Refrigerante', 6));
			expect(pedido.itensPedido[1]).toEqual(new ItemSimples(1, 11, 'Suco', 8));
		});
	});

	describe('itens pizza', () => {
		it('adiciona uma pizza corretamente ao pedido', async () => {
			const pedido = await gestor.adicionarItemPizza({
				quantidade: 1,
				tamanho: 'MEDIA',
				preco: 40,
				saboresEscolhidos: [{ idPizza: 1, nome: 'Margherita' }],
			});

			expect(pedido.itensPedido).toHaveLength(1);
			expect(pedido.itensPedido[0]).toEqual(new ItemPizza(1, [new SaborEscolhido(new Pizza(1, 'Margherita'))], TamanhoPizza.MEDIA, 40));
		});

		it('junta pizzas iguais somando as quantidades', async () => {
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
			expect(pedido.itensPedido[0]).toEqual(new ItemPizza(3, [new SaborEscolhido(new Pizza(1, 'Margherita'))], TamanhoPizza.MEDIA, 40));
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
			expect(pedido.itensPedido[0]).toEqual(new ItemPizza(1, [new SaborEscolhido(new Pizza(1, 'Margherita'))], TamanhoPizza.MEDIA, 40));
			expect(pedido.itensPedido[1]).toEqual(new ItemPizza(1, [new SaborEscolhido(new Pizza(1, 'Margherita'))], TamanhoPizza.GRANDE, 55));
		});

		it('mantém separadas pizzas de sabores diferentes', async () => {
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
			expect(pedido.itensPedido[0]).toEqual(new ItemPizza(1, [new SaborEscolhido(new Pizza(1, 'Margherita'))], TamanhoPizza.MEDIA, 40));
			expect(pedido.itensPedido[1]).toEqual(new ItemPizza(1, [new SaborEscolhido(new Pizza(2, 'Calabresa'))], TamanhoPizza.MEDIA, 40));
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
			expect(pedido.itensPedido[0]).toEqual(new ItemPizza(1, [new SaborEscolhido(new Pizza(1, 'Margherita'))], TamanhoPizza.MEDIA, 40));
			expect(pedido.itensPedido[1]).toEqual(new ItemSimples(1, 10, 'Refrigerante', 6));
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

			const comCliente = await gestor.adicionarCliente(1);
			expect(comCliente.cliente).toEqual(new Cliente(1));
		});

		it('remove cliente do pedido', async () => {
			await gestor.adicionarItemSimples({
				quantidade: 1,
				idProduto: 10,
				nome: 'Refrigerante',
				preco: 6,
			});
			await gestor.adicionarCliente(1);

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
		});
	});
});
