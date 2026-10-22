import { RepositorioPizzaNoServidor } from '../../pizza/repositorio/repositorio-pizza-no-servidor';
import { GestorRegistroPedido } from '../dominio/gestor-registro-pedido';
import { RepositorioPedidoNoServidor } from '../repositorio/repositorio-pedido-no-servidor';
import { ServicoHttpComFetch } from '../../infra/http/servico-http-com-fetch';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { RepositorioProdutoSimplesNoServidor } from '../../produto-simples/repositorio/repositorio-produto-simples-no-servidor';
import { RepositorioClienteNoServidor } from '../../cliente/repositorio/repositorio-cliente-no-servidor';
import { TipoAtendimento } from '../dominio/tipo-atendimento';
import { TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { Pizza } from '../../pizza/dominio/pizza';
import { CategoriaCardapio, DadosItemPizza, DadosItemSimples, VisaoRegistroPedido } from '../view/visao-registro-pedido';
import { VisaoEtapaProdutos } from '../view/visao-etapa-produtos';
import { VisaoPersonalizacaoPizza } from '../view/visao-personalizacao-pizza';
import { VisaoEtapaAtendimento } from '../view/visao-etapa-atendimento';
import { VisaoResumoPedido } from '../view/visao-resumo-pedido';

type VisoesRegistroPedido = {
	principal: VisaoRegistroPedido;
	produtos: VisaoEtapaProdutos;
	personalizacao: VisaoPersonalizacaoPizza;
	atendimento: VisaoEtapaAtendimento;
	resumo: VisaoResumoPedido;
};

export class ControladoraRegistroPedido {
	private readonly visoes: VisoesRegistroPedido;
	private readonly gestor: GestorRegistroPedido;
	private opcoesPizzaParaSegundoSabor: Map<number, Pizza> = new Map();

	constructor(visoes: VisoesRegistroPedido) {
		this.visoes = visoes;

		const servicoHttp = new ServicoHttpComFetch();
		this.gestor = new GestorRegistroPedido(
			new RepositorioPedidoNoServidor(servicoHttp),
			new RepositorioPizzaNoServidor(servicoHttp),
			new RepositorioProdutoSimplesNoServidor(servicoHttp),
			new RepositorioClienteNoServidor(servicoHttp),
		);
	}

	obterCardapio() {
		const filtro = this.visoes.produtos.obterFiltroCardapio();

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
			const pizzas = await this.gestor.obterPizzasParaCardapio(busca);
			this.visoes.produtos.exibirPizzasNoCardapio(pizzas);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter as pizzas para o cardápio.';
			this.visoes.produtos.exibirErroAoObterPizzasParaCardapio(mensagemErro);
		}
	}

	private async obterProdutosSimplesParaCardapio(tipo: TipoProdutoSimples, busca: string = '') {
		try {
			const produtos = await this.gestor.obterProdutosSimples(tipo, busca);
			this.visoes.produtos.exibirProdutosSimplesNoCardapio(produtos, tipo);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter os produtos simples para o cardápio.';
			this.visoes.produtos.exibirErroAoObterProdutosSimplesParaCardapio(mensagemErro, tipo);
		}
	}

	async personalizarItemPizza(idPizza: number, tamanho: TamanhoPizza): Promise<void> {
		try {
			const pizza = await this.gestor.obterPizzaPorId(idPizza);
			this.visoes.personalizacao.exibirPizzaParaPersonalizacao(pizza, tamanho);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível personalizar o produto.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async obterPizzasParaSegundoSabor(): Promise<void> {
		try {
			const filtro = this.visoes.personalizacao.obterFiltroSegundoSabor();
			const pizzas = await this.gestor.obterPizzas(filtro);
			this.atualizarOpcoesPizzaParaSegundoSabor(pizzas);

			this.visoes.personalizacao.exibirPizzasParaSegundoSabor(pizzas);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível obter as pizzas para o segundo sabor.';
			this.visoes.personalizacao.exibirErroAoObterPizzasParaSegundoSabor(mensagem);
		}
	}

	private atualizarOpcoesPizzaParaSegundoSabor(pizzas: Pizza[]): void {
		this.opcoesPizzaParaSegundoSabor.clear();
		this.opcoesPizzaParaSegundoSabor = new Map(pizzas.map((pizza) => [pizza.id, pizza]));
	}

	async adicionarPizzaComoSegundoSabor(idPizza: number): Promise<void> {
		try {
			const pizza = this.opcoesPizzaParaSegundoSabor.get(idPizza);
			if (!pizza) {
				throw new Error('Pizza não encontrada');
			}

			this.visoes.personalizacao.adicionarPizzaComoSegundoSabor(pizza);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar a pizza como segundo sabor.';
			this.visoes.personalizacao.exibirErroAoObterPizzasParaSegundoSabor(mensagem);
		}
	}

	async adicionarPersonalizacaoItemPizza(): Promise<void> {
		try {
			const dados = this.visoes.personalizacao.obterDadosPersonalizacaoItemPizza();
			const pedido = await this.gestor.adicionarItemPizza(dados);
			this.visoes.resumo.exibirResumoPedido(pedido);
			this.visoes.personalizacao.fecharModal();
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar a personalização ao pedido.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
			this.visoes.personalizacao.fecharModal();
		}
	}

	async adicionarItemPizza(dados: DadosItemPizza): Promise<void> {
		try {
			const pedido = await this.gestor.adicionarItemPizza(dados);
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar o produto ao pedido.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async adicionarItemSimples(dados: DadosItemSimples): Promise<void> {
		try {
			const pedido = await this.gestor.adicionarItemSimples(dados);
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar o produto ao pedido.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async obterClientes() {
		try {
			const busca = this.visoes.atendimento.obterFiltroCliente();
			const clientes = await this.gestor.obterClientes(busca);
			this.visoes.atendimento.exibirClientes(clientes);
		} catch (erro) {
			const mensagemErro = erro instanceof Error ? erro.message : 'Ocorreu um erro ao obter as pizzas para o cardápio.';
			this.visoes.atendimento.exibirErroAoObterClientes(mensagemErro);
		}
	}

	async adicionarCliente(idCliente: number): Promise<void> {
		try {
			const pedido = await this.gestor.adicionarCliente(idCliente);
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível adicionar o cliente ao pedido.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async removerCliente(): Promise<void> {
		try {
			const pedido = await this.gestor.removerCliente();
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível remover o cliente do pedido.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async alterarQuantidade(indice: number, delta: number): Promise<void> {
		try {
			const pedido = await this.gestor.alterarQuantidade(indice, delta);
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível alterar a quantidade do item.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async removerItem(indice: number): Promise<void> {
		try {
			const pedido = await this.gestor.removerItem(indice);
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível remover o item.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async atualizarTipoAtendimento(tipoAtendimento: TipoAtendimento): Promise<void> {
		try {
			const pedido = await this.gestor.atualizarTipoAtendimento(tipoAtendimento);
			this.visoes.resumo.exibirResumoPedido(pedido);
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível atualizar o tipo de atendimento.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
		}
	}

	async registrarPedido(): Promise<void> {
		try {
			await this.gestor.registrarPedido();
			this.visoes.resumo.exibirMensagemPedidoRegistrado();
			this.visoes.principal.redirecionarParaHome();
		} catch (erro) {
			const mensagem = erro instanceof Error ? erro.message : 'Não foi possível registrar o pedido.';
			this.visoes.resumo.exibirErroNoPedido(mensagem);
			this.visoes.resumo.habilitarRegistroPedido();
		}
	}
}
