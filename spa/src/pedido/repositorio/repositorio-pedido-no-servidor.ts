import { RepositorioPedido } from './repositorio-pedido';
import { ServicoHttp } from '../../infra/http/servico-http';
import { API } from '../../infra/constantes';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { HttpError } from '../../infra/exceptions/http-error';
import { Pedido } from '../dominio/pedido';
import { ItemPizza } from '../dominio/item-pizza';
import { ItemSimples } from '../dominio/item-simples';
import { ResumoValoresPedido } from '../dominio/resumo-valores-pedido';

export class RepositorioPedidoNoServidor implements RepositorioPedido {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterResumoValores(pedido: Pedido): Promise<ResumoValoresPedido> {
		try {
			const resultado = await this.servicoHttp.post(`${API}/pedidos/valores`, this.obterCorpoRequisicaoParaPedido(pedido));

			const resumo = resultado.data.resumoValores;
			const valorTotalPromocao = resumo.promocoes.map((promocao: { valorDesconto: number }) => promocao.valorDesconto).reduce((total: number, valor: number) => total + valor, 0);
			const valorTotalFidelidade = resumo.fidelidade?.valorDesconto ?? 0;

			return new ResumoValoresPedido(valorTotalPromocao, valorTotalFidelidade, resumo.valorTotalDescontos, resumo.taxaEntrega, resumo.valorTotal);
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : 'Ocorreu um erro ao obter o resumo de descontos do pedido.';
			throw new RepositorioError(mensagemErro);
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
						ingredientesEscolhidos: [],
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
}
