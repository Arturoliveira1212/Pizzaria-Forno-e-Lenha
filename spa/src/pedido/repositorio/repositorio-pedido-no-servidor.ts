import { RepositorioPedido } from './repositorio-pedido';
import { ServicoHttp } from '../../infra/http/servico-http';
import { API } from '../../infra/constantes';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { HttpError } from '../../infra/exceptions/http-error';
import { Pedido } from '../dominio/pedido';
import { ItemPizza } from '../dominio/item-pizza';
import { ItemSimples } from '../dominio/item-simples';
import { ResumoValoresPedido } from '../dominio/resumo-valores-pedido';
import { PedidoListagem } from '../dominio/pedido-listagem';

export class RepositorioPedidoNoServidor implements RepositorioPedido {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterPedidos(identificador: string = ''): Promise<PedidoListagem[]> {
		try {
			const parametros = new URLSearchParams();
			if (identificador !== '') {
				parametros.set('identificador', identificador);
			}

			const resultado = await this.servicoHttp.get(`${API}/pedidos?${parametros.toString()}`);

			return resultado.data.pedidos.map(
				(pedido: { id: number; data: string; nomeCliente: string | null; tipoAtendimento: string; status: string; proximosStatus: string[]; valorTotal: number }) =>
					new PedidoListagem(pedido.id, pedido.data, pedido.nomeCliente, pedido.tipoAtendimento, pedido.status, pedido.proximosStatus, pedido.valorTotal),
			);
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao obter a lista de pedidos.');
		}
	}

	async alterarStatus(idPedido: number, status: string): Promise<void> {
		try {
			await this.servicoHttp.patch(`${API}/pedidos/status`, { idPedido, status });
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao alterar o status do pedido.');
		}
	}

	async obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido> {
		try {
			const resultado = await this.servicoHttp.post(`${API}/pedidos/valores`, this.obterCorpoRequisicaoParaPedido(pedido));

			const resumo = resultado.data.resumoValores;
			const valorTotalPromocao = resumo.promocoes.map((promocao: { valorDesconto: number }) => promocao.valorDesconto).reduce((total: number, valor: number) => total + valor, 0);
			const valorTotalFidelidade = resumo.fidelidade?.valorDesconto ?? 0;

			return new ResumoValoresPedido(valorTotalPromocao, valorTotalFidelidade, resumo.valorTotalDescontos, resumo.taxaEntrega, resumo.valorTotal);
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao obter o resumo de valores do pedido.');
		}
	}

	async registrarPedido(pedido: Pedido): Promise<void> {
		try {
			await this.servicoHttp.post(`${API}/pedidos`, this.obterCorpoRequisicaoParaPedido(pedido));
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao registrar o pedido.');
		}
	}

	private obterCorpoRequisicaoParaPedido(pedido: Pedido): any {
		const itensPedido = pedido.itensPedido.map((item) => {
			if (item instanceof ItemPizza) {
				return {
					quantidade: item.quantidade,
					tamanhoPizza: item.tamanhoPizza,
					saboresEscolhidos: item.saboresEscolhidos.map((sabor) => ({
						idPizza: sabor.pizza.id,
						ingredientesEscolhidos: sabor.ingredientesEscolhidos.map((ingredienteEscolhido) => ({
							idIngrediente: ingredienteEscolhido.ingrediente.id,
							papel: ingredienteEscolhido.papel,
						})),
					})),
				};
			} else if (item instanceof ItemSimples) {
				return {
					quantidade: item.quantidade,
					idProduto: item.idProduto,
				};
			}
		});

		return {
			tipoAtendimento: pedido.tipoAtendimento,
			idCliente: pedido.cliente?.id,
			itensPedido: itensPedido,
		};
	}

	private tratarErro(error: any, mensagemPadrao: string): never {
		const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : mensagemPadrao;
		throw new RepositorioError(mensagemErro);
	}
}
