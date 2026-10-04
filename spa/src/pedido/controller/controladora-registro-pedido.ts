import { RepositorioPizzaNoServidor } from '../../pizza/repositorio/repositorio-pizza-no-servidor';
import { GestorPedido } from '../dominio/gestor-pedido';
import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { VisaoRegistroPedido } from '../view/visao-registro-pedido';
import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { CategoriaCardapio } from '../categoria-cardapio';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { GestorPizza } from '../../pizza/dominio/gestor-pizza';
import { GestorProdutoSimples } from '../../produto-simples/dominio/gestor-produto-simples';
import { RepositorioProdutoSimplesNoServidor } from '../../produto-simples/repositorio/repositorio-produto-simples-no-servidor';
import { GestorCliente } from '../../cliente/dominio/gestor-cliente';
import { RepositorioClienteNoServidor } from '../../cliente/repositorio/repositorio-cliente-no-servidor';

export class ControladoraRegistroPedido {
	private readonly visaoRegistroPedido: VisaoRegistroPedido;
	private readonly gestorPedido: GestorPedido;
	private readonly gestorPizza: GestorPizza;
	private readonly gestorProdutoSimples: GestorProdutoSimples;
	private readonly gestorCliente: GestorCliente;

	constructor(visaoRegistroPedido: VisaoRegistroPedido) {
		this.visaoRegistroPedido = visaoRegistroPedido;

		const servicoHttp = new ServicoHttpComFetch();
		this.gestorPedido = new GestorPedido(new RepositorioPedidoNoServidor(servicoHttp));
		this.gestorPizza = new GestorPizza(new RepositorioPizzaNoServidor(servicoHttp));
		this.gestorProdutoSimples = new GestorProdutoSimples(new RepositorioProdutoSimplesNoServidor(servicoHttp));
		this.gestorCliente = new GestorCliente(new RepositorioClienteNoServidor(servicoHttp));
	}

	obterCardapio() {
		const filtro = this.visaoRegistroPedido.obterFiltroCardapio();

		switch (filtro.categoria) {
			case CategoriaCardapio.PIZZA:
				this.obterPizzasParaCardapio(filtro.busca);
				break;
			case CategoriaCardapio.BEBIDA:
				this.obterProdutosSimplesParaCardapio(TipoProdutoSimples.BEBIDA, filtro.busca);
				break;
			case CategoriaCardapio.SOBREMESA:
				this.obterProdutosSimplesParaCardapio(TipoProdutoSimples.SOBREMESA, filtro.busca);
				break;
		}
	}

	private async obterPizzasParaCardapio(busca: string = '') {
		try {
			const pizzas = await this.gestorPizza.obterPizzasParaCardapio(busca);
			this.visaoRegistroPedido.exibirPizzasNoCardapio(pizzas);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter as pizzas para o cardápio.';
			this.visaoRegistroPedido.exibirErroAoObterPizzasParaCardapio(mensagemErro);
		}
	}

	private async obterProdutosSimplesParaCardapio(tipo: TipoProdutoSimples, busca: string = '') {
		try {
			const produtos = await this.gestorProdutoSimples.obterProdutosSimples(tipo, busca);
			this.visaoRegistroPedido.exibirProdutosSimplesNoCardapio(produtos, tipo);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter os produtos simples para o cardápio.';
			this.visaoRegistroPedido.exibirErroAoObterProdutosSimplesParaCardapio(mensagemErro, tipo);
		}
	}

	async obterClientes() {
		try {
			const busca = this.visaoRegistroPedido.obterFiltroCliente();
			const clientes = await this.gestorCliente.obterClientes(busca);
			this.visaoRegistroPedido.exibirClientes(clientes);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter as pizzas para o cardápio.';
			this.visaoRegistroPedido.exibirErroAoObterClientes(mensagemErro);
		}
	}

	async adicionarItemPizza(item: { quantidade: number; tamanho: string; preco: number; saboresEscolhidos: { idPizza: number; nome: string }[] }): Promise<void> {
		try {
			const pedido = await this.gestorPedido.adicionarItemPizza(item);
			this.visaoRegistroPedido.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar o produto ao pedido.';
			this.visaoRegistroPedido.exibirErroNoPedido(mensagem);
		}
	}

	async adicionarItemSimples(item: { quantidade: number; idProduto: number; nome: string; preco: number }): Promise<void> {
		try {
			const pedido = await this.gestorPedido.adicionarItemSimples(item);
			this.visaoRegistroPedido.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar o produto ao pedido.';
			this.visaoRegistroPedido.exibirErroNoPedido(mensagem);
		}
	}

	async adicionarCliente(idCliente: string): Promise<void> {
		try {
			const pedido = await this.gestorPedido.adicionarCliente(idCliente);
			this.visaoRegistroPedido.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar o cliente ao pedido.';
			this.visaoRegistroPedido.exibirErroNoPedido(mensagem);
		}
	}

	async removerCliente(): Promise<void> {
		try {
			const pedido = await this.gestorPedido.removerCliente();
			this.visaoRegistroPedido.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível remover o cliente do pedido.';
			this.visaoRegistroPedido.exibirErroNoPedido(mensagem);
		}
	}

	async alterarQuantidade(indice: number, delta: number): Promise<void> {
		try {
			const pedido = await this.gestorPedido.alterarQuantidade(indice, delta);
			this.visaoRegistroPedido.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível alterar a quantidade do item.';
			this.visaoRegistroPedido.exibirErroNoPedido(mensagem);
		}
	}

	async removerItem(indice: number): Promise<void> {
		try {
			const pedido = await this.gestorPedido.removerItem(indice);
			this.visaoRegistroPedido.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível remover o item.';
			this.visaoRegistroPedido.exibirErroNoPedido(mensagem);
		}
	}
}
