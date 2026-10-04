import { Cliente } from '../../cliente/dominio/cliente';
import { ItemPedido } from './item-pedido';
import { ItemPizza } from './item-pizza';
import { ItemSimples } from './item-simples';
import { ResumoDescontosPedido } from './resumo-descontos-pedido';
import { TipoAtendimento } from './tipo-atendimento';
import { DominioError } from '../../infra/exceptions/dominio-error';

export class Pedido {
	public readonly tipoAtendimento: TipoAtendimento = TipoAtendimento.BALCAO;
	public readonly cliente?: Cliente = undefined;
	public readonly itensPedido: ItemPedido[] = [];
	public readonly valorDescontoPromocao: number = 0;
	public readonly valorDescontoFidelidade: number = 0;
	public readonly valorTotal: number = 0;

	constructor(
		tipoAtendimento: TipoAtendimento = TipoAtendimento.BALCAO,
		cliente?: Cliente,
		itensPedido: ItemPedido[] = [],
		valorDescontoPromocao: number = 0,
		valorDescontoFidelidade: number = 0,
		valorTotal: number = 0,
	) {
		this.tipoAtendimento = tipoAtendimento;
		this.cliente = cliente;
		this.itensPedido = itensPedido;
		this.valorDescontoPromocao = valorDescontoPromocao;
		this.valorDescontoFidelidade = valorDescontoFidelidade;
		this.valorTotal = valorTotal;
	}

	adicionarItemPizza(itemPizza: ItemPizza): void {
		for (const i in this.itensPedido) {
			const item = this.itensPedido[i];
			if (item instanceof ItemPizza && item.tamanhoPizza === itemPizza.tamanhoPizza) {
				const saboresExistentes = item.saboresEscolhidos.map((sabor) => sabor.pizza.id);
				const novosSabores = itemPizza.saboresEscolhidos.map((sabor) => sabor.pizza.id);

				if (this.saboresIguais(saboresExistentes, novosSabores)) {
					this.itensPedido[i] = item.criarItemPizzaComNovaQuantidade(item.quantidade + itemPizza.quantidade);
					return;
				}
			}
		}

		this.itensPedido.push(itemPizza);
	}

	private saboresIguais(a: number[], b: number[]): boolean {
		if (a.length !== b.length) {
			return false;
		}

		const ordenadoA = [...a].sort();
		const ordenadoB = [...b].sort();

		return ordenadoA.every((id, indice) => id === ordenadoB[indice]);
	}

	adicionarItemSimples(itemSimples: ItemSimples): void {
		for (const i in this.itensPedido) {
			const item = this.itensPedido[i];
			if (item instanceof ItemSimples && item.idProduto === itemSimples.idProduto) {
				this.itensPedido[i] = item.criarItemSimplesComNovaQuantidade(item.quantidade + itemSimples.quantidade);
				return;
			}
		}

		this.itensPedido.push(itemSimples);
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
		return new Pedido(this.tipoAtendimento, cliente, this.itensPedido, this.valorDescontoPromocao, this.valorDescontoFidelidade, this.valorTotal);
	}

	criarNovoPedidoComDescontos(resumoDescontosPedido: ResumoDescontosPedido): Pedido {
		return new Pedido(
			this.tipoAtendimento,
			this.cliente,
			this.itensPedido,
			resumoDescontosPedido.valorDescontoPromocao,
			resumoDescontosPedido.valorDescontoFidelidade,
			resumoDescontosPedido.valorTotal,
		);
	}

	estaVazio(): boolean {
		return this.itensPedido.length === 0;
	}
}
