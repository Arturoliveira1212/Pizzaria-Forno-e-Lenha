import { Pedido } from './pedido';
import { RepositorioPedido } from '../repositorio/repositorio-pedido';
import { ItemPizza } from './item-pizza';
import { ItemSimples } from './item-simples';
import { SaborEscolhido } from './sabor-escolhido';
import { TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { ResumoValoresPedido } from './resumo-valores-pedido';
import { Pizza } from '../../pizza/dominio/pizza';
import { Cliente } from '../../cliente/dominio/cliente';
import { TipoAtendimento } from './tipo-atendimento';
import { DominioError } from '../../infra/exceptions/dominio-error';

export class GestorPedido {
	private pedidoAtual: Pedido = new Pedido();

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

	async adicionarCliente(idCliente: number): Promise<Pedido> {
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

	async atualizarTipoAtendimento(tipoAtendimento: TipoAtendimento): Promise<Pedido> {
		this.pedidoAtual = this.pedidoAtual.criarNovoPedidoComTipoAtendimento(tipoAtendimento);
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	async registrarPedido(): Promise<void> {
		if (this.pedidoAtual.estaVazio()) {
			throw new DominioError('Não é possível registrar um pedido sem itens.');
		}

		await this.repositorioPedido.registrarPedido(this.pedidoAtual);
	}

	private async atualizarPedido(): Promise<void> {
		if (this.pedidoAtual.estaVazio()) {
			this.pedidoAtual = this.pedidoAtual.criarNovoPedidoComValores(new ResumoValoresPedido(0, 0, 0, 0, 0));
			return;
		}

		const resumoValores = await this.obterResumoValores(this.pedidoAtual);
		this.pedidoAtual = this.pedidoAtual.criarNovoPedidoComValores(resumoValores);
	}

	private obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido> {
		return this.repositorioPedido.obterResumoValores(pedido);
	}
}
