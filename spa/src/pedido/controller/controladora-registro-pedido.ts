import { RepositorioPizzaNoServidor } from '../../pizza/repositorio/repositorio-pizza-no-servidor';
import { GestorPedido } from '../dominio/gestor-pedido';
import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { VisaoRegistroPedido } from '../view/visao-registro-pedido';
import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { CategoriaCardapio } from '../dominio/categoria-cardapio';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { GestorPizza } from '../../pizza/dominio/gestor-pizza';
import { GestorProdutoSimples } from '../../produto-simples/dominio/gestor-produto-simples';
import { RepositorioProdutoSimplesNoServidor } from '../../produto-simples/repositorio/repositorio-produto-simples-no-servidor';

export class ControladoraRegistroPedido {
	private readonly visaoRegistroPedido: VisaoRegistroPedido;
	private readonly gestorPedido: GestorPedido;
	private readonly gestorPizza: GestorPizza;
	private readonly gestorProdutoSimples: GestorProdutoSimples;

	constructor(visaoRegistroPedido: VisaoRegistroPedido) {
		this.visaoRegistroPedido = visaoRegistroPedido;

		const servicoHttp = new ServicoHttpComFetch();
		this.gestorPedido = new GestorPedido(new RepositorioPedidoNoServidor(servicoHttp));
		this.gestorPizza = new GestorPizza(new RepositorioPizzaNoServidor(servicoHttp));
		this.gestorProdutoSimples = new GestorProdutoSimples(new RepositorioProdutoSimplesNoServidor(servicoHttp));
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
}
