import { Pedido } from './pedido';
import { RepositorioPedido } from '../repositorio/repositorio-pedido';
import { ItemPizza } from './item-pizza';
import { ItemSimples } from './item-simples';
import { SaborEscolhido } from './sabor-escolhido';
import { TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { ResumoDescontosPedido } from './resumo-descontos-pedido';
import { Pizza } from '../../pizza/dominio/pizza';
import { Cliente } from '../../cliente/dominio/cliente';

export class GestorPedido {
	private pedidoAtual = new Pedido();

	constructor(private readonly repositorioPedido: RepositorioPedido) {}

	async adicionarItemPizza(item: { quantidade: number; tamanho: string; preco: number; saboresEscolhidos: { idPizza: number; nome: string }[] }): Promise<Pedido> {
		const itemPizza = this.criarItemPizza(item);
		this.pedidoAtual.adicionarItemPizza(itemPizza);
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	private criarItemPizza(item: { quantidade: number; tamanho: string; preco: number; saboresEscolhidos: { idPizza: number; nome: string }[] }) {
		const sabores = [];
		for (const sabor of item.saboresEscolhidos) {
			sabores.push(new SaborEscolhido(new Pizza(sabor.idPizza, sabor.nome)));
		}

		return new ItemPizza(item.quantidade, sabores, item.tamanho as TamanhoPizza, item.preco);
	}

	async adicionarItemSimples(item: { quantidade: number; idProduto: number; nome: string; preco: number }): Promise<Pedido> {
		this.pedidoAtual.adicionarItemSimples(new ItemSimples(item.quantidade, item.idProduto, item.nome, item.preco));
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	async adicionarCliente(idCliente: string): Promise<Pedido> {
		this.pedidoAtual = this.pedidoAtual.criarNovoPedidoComCliente(new Cliente(idCliente));
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	async removerCliente(): Promise<Pedido> {
		this.pedidoAtual = this.pedidoAtual.criarNovoPedidoComCliente(undefined);
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	async alterarQuantidade(indice: number, delta: number): Promise<Pedido> {
		this.pedidoAtual.alterarQuantidade(indice, delta);
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	async removerItem(indice: number): Promise<Pedido> {
		this.pedidoAtual.removerItem(indice);
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	private async atualizarPedido(): Promise<void> {
		if (this.pedidoAtual.estaVazio()) {
			return;
		}

		const resumoDescontos = await this.obterResumoDescontos(this.pedidoAtual);
		this.pedidoAtual = this.pedidoAtual.criarNovoPedidoComDescontos(resumoDescontos);
	}

	private obterResumoDescontos(pedido: Pedido): Promise<ResumoDescontosPedido> {
		return this.repositorioPedido.obterResumoDescontos(pedido);
	}
}
