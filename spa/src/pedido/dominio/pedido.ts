import { Cliente } from '../../cliente/dominio/cliente';
import { ItemPedido } from './item-pedido';
import { ItemPizza } from './item-pizza';
import { ItemSimples } from './item-simples';
import { ResumoValoresPedido } from './resumo-valores-pedido';
import { TipoAtendimento } from './tipo-atendimento';
import { DominioError } from '../../infra/exceptions/dominio-error';
import { SaborEscolhido } from './sabor-escolhido';

export class Pedido {
	public readonly tipoAtendimento: TipoAtendimento = TipoAtendimento.BALCAO;
	public readonly cliente?: Cliente = undefined;
	public readonly itensPedido: ItemPedido[] = [];
	public readonly valorDescontoPromocao: number = 0;
	public readonly valorDescontoFidelidade: number = 0;
	public readonly taxaEntrega: number = 0;
	public readonly valorTotal: number = 0;

	constructor(
		tipoAtendimento: TipoAtendimento = TipoAtendimento.BALCAO,
		cliente?: Cliente,
		itensPedido: ItemPedido[] = [],
		valorDescontoPromocao: number = 0,
		valorDescontoFidelidade: number = 0,
		taxaEntrega: number = 0,
		valorTotal: number = 0,
	) {
		this.tipoAtendimento = tipoAtendimento;
		this.cliente = cliente;
		this.itensPedido = itensPedido;
		this.valorDescontoPromocao = valorDescontoPromocao;
		this.valorDescontoFidelidade = valorDescontoFidelidade;
		this.taxaEntrega = taxaEntrega;
		this.valorTotal = valorTotal;
	}

	adicionarItemPizza(itemPizza: ItemPizza): void {
		for (const i in this.itensPedido) {
			const item = this.itensPedido[i];

			if (item instanceof ItemPizza && this.itensPizzaSaoIguais(item, itemPizza)) {
				this.itensPedido[i] = item.criarItemPizzaComNovaQuantidade(item.quantidade + itemPizza.quantidade);
				return;
			}
		}

		this.itensPedido.push(itemPizza);
	}

	private itensPizzaSaoIguais(itemA: ItemPizza, itemB: ItemPizza): boolean {
		return itemA.tamanhoPizza === itemB.tamanhoPizza && this.saboresIguais(itemA.saboresEscolhidos, itemB.saboresEscolhidos);
	}

	private saboresIguais(a: SaborEscolhido[], b: SaborEscolhido[]): boolean {
		if (a.length !== b.length) return false;

		const chavesA = a.map((s) => s.chave()).sort();
		const chavesB = b.map((s) => s.chave()).sort();

		return chavesA.every((chave, indice) => chave === chavesB[indice]);
	}

	adicionarItemSimples(itemSimples: ItemSimples): void {
		for (const i in this.itensPedido) {
			const item = this.itensPedido[i];

			if (item instanceof ItemSimples && this.itensSimplesSaoIguais(item, itemSimples)) {
				this.itensPedido[i] = item.criarItemSimplesComNovaQuantidade(item.quantidade + itemSimples.quantidade);
				return;
			}
		}

		this.itensPedido.push(itemSimples);
	}

	private itensSimplesSaoIguais(itemA: ItemSimples, itemB: ItemSimples): boolean {
		return itemA.idProduto === itemB.idProduto;
	}

	alterarQuantidade(indice: number, delta: number): void {
		const item = this.itensPedido[indice];
		if (!item) {
			throw new DominioError('Item do pedido não encontrado.');
		}

		const novaQuantidade = item.quantidade + delta;
		if (novaQuantidade <= 0) {
			this.removerItem(indice);
			return;
		}

		if (item instanceof ItemPizza) {
			this.itensPedido[indice] = item.criarItemPizzaComNovaQuantidade(novaQuantidade);
		} else if (item instanceof ItemSimples) {
			this.itensPedido[indice] = item.criarItemSimplesComNovaQuantidade(novaQuantidade);
		}
	}

	removerItem(indice: number): void {
		if (indice < 0 || indice >= this.itensPedido.length) {
			throw new DominioError('Item do pedido não encontrado.');
		}

		this.itensPedido.splice(indice, 1);
	}

	criarNovoPedidoComCliente(cliente?: Cliente): Pedido {
		return new Pedido(this.tipoAtendimento, cliente, this.itensPedido, this.valorDescontoPromocao, this.valorDescontoFidelidade, this.taxaEntrega, this.valorTotal);
	}

	criarNovoPedidoComValores(resumoValoresPedido: ResumoValoresPedido): Pedido {
		return new Pedido(
			this.tipoAtendimento,
			this.cliente,
			this.itensPedido,
			resumoValoresPedido.valorDescontoPromocao,
			resumoValoresPedido.valorDescontoFidelidade,
			resumoValoresPedido.taxaEntrega,
			resumoValoresPedido.valorTotal,
		);
	}

	criarNovoPedidoComTipoAtendimento(tipoAtendimento: TipoAtendimento): Pedido {
		return new Pedido(tipoAtendimento, this.cliente, this.itensPedido, this.valorDescontoPromocao, this.valorDescontoFidelidade, this.taxaEntrega, this.valorTotal);
	}

	criarNovoPedidoComItens(itensPedido: ItemPedido[]): Pedido {
		return new Pedido(this.tipoAtendimento, this.cliente, itensPedido, this.valorDescontoPromocao, this.valorDescontoFidelidade, this.taxaEntrega, this.valorTotal);
	}

	estaVazio(): boolean {
		return this.itensPedido.length === 0;
	}
}
