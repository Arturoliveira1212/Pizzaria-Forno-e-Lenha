import { RepositorioPedido } from './repositorio-pedido';
import { ServicoHttp } from '../../infra/http/servico-http';
import { API } from '../../infra/constantes';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { HttpError } from '../../infra/exceptions/http-error';
import { Pedido } from '../dominio/pedido';
import { ResumoDescontosPedido } from '../dominio/resumo-descontos-pedido';
import { ItemPizza } from '../dominio/item-pizza';
import { ItemSimples } from '../dominio/item-simples';

export class RepositorioPedidoNoServidor implements RepositorioPedido {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterResumoDescontos(pedido: Pedido): Promise<ResumoDescontosPedido> {
		try {
			const resultado = await this.servicoHttp.post(`${API}/pedidos/descontos`, this.obterCorpoRequisicaoParaPedido(pedido));

			const resumo = resultado.data.resumoDescontos;
			const valorTotalPromocao = resumo.promocoes.map((promocao: { valorDesconto: number }) => promocao.valorDesconto).reduce((total: number, valor: number) => total + valor, 0);
			const valorTotalFidelidade = resumo.fidelidade?.valorDesconto ?? 0;

			return new ResumoDescontosPedido(valorTotalPromocao, valorTotalFidelidade, resumo.valorTotal);
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
