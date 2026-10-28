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
import { RepositorioPizza } from '../../pizza/repositorio/repositorio-pizza';
import { RepositorioProdutoSimples } from '../../produto-simples/repositorio/repositorio-produto-simples';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { ProdutoSimples } from '../../produto-simples/dominio/produto-simples';
import { RepositorioCliente } from '../../cliente/repositorio/repositorio-cliente';
import { PizzaParaCardapio } from '../../pizza/dominio/pizza-para-cardapio';
import { IngredienteEscolhido } from './ingrediente-escolhido';
import { Ingrediente } from '../../ingrediente/dominio/ingrediente';
import { DadosItemPizza, DadosItemSimples } from '../view/visao-registro-pedido';

export class GestorRegistroPedido {
	private pedidoAtual: Pedido = new Pedido();

	constructor(
		private readonly repositorioPedido: RepositorioPedido,
		private readonly repositorioPizza: RepositorioPizza,
		private readonly repositorioProdutoSimples: RepositorioProdutoSimples,
		private readonly repositorioCliente: RepositorioCliente,
	) {}

	async obterPizzasParaCardapio(busca: string): Promise<PizzaParaCardapio[]> {
		return this.repositorioPizza.obterPizzasParaCardapio(busca);
	}

	async obterPizzas(busca: string): Promise<Pizza[]> {
		return this.repositorioPizza.obterPizzas(busca);
	}

	async obterPizzaPorId(idPizza: number): Promise<Pizza> {
		return this.repositorioPizza.obterPizzaPorId(idPizza);
	}

	async obterProdutosSimples(tipo: TipoProdutoSimples, busca: string): Promise<ProdutoSimples[]> {
		return this.repositorioProdutoSimples.obterProdutosSimples(tipo, busca);
	}

	async adicionarItemPizza(dados: DadosItemPizza): Promise<Pedido> {
		const itemPizza = this.criarItemPizza(dados);
		this.pedidoAtual.adicionarItemPizza(itemPizza);
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	private criarItemPizza(dados: DadosItemPizza) {
		const sabores = [];

		for (const sabor of dados.saboresEscolhidos) {
			const ingredientesEscolhidos = sabor.ingredientesEscolhidos.map((ingrediente) => {
				return new IngredienteEscolhido(new Ingrediente(ingrediente.idIngrediente), ingrediente.papel);
			});
			sabores.push(new SaborEscolhido(new Pizza(sabor.idPizza, sabor.nome), ingredientesEscolhidos));
		}

		return new ItemPizza(dados.quantidade, sabores, dados.tamanho as TamanhoPizza, dados.preco);
	}

	async adicionarItemSimples(dados: DadosItemSimples): Promise<Pedido> {
		this.pedidoAtual.adicionarItemSimples(new ItemSimples(dados.quantidade, dados.idProduto, dados.nome, dados.preco));
		await this.atualizarPedido();

		return this.pedidoAtual;
	}

	async obterClientes(identificador: string = ''): Promise<Cliente[]> {
		return this.repositorioCliente.obterClientes(identificador);
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
