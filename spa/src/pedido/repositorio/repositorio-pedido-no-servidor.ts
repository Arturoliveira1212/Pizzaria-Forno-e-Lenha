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
import { DetalhesAdicionalSabor, DetalhesItemPedido, DetalhesPedido, DetalhesSaborEscolhido } from '../dominio/detalhes-pedido';

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

	async obterDetalhesPedido(idPedido: number): Promise<DetalhesPedido> {
		try {
			const resultado = await this.servicoHttp.get(`${API}/pedidos/${idPedido}`);

			const pedido = resultado.data.pedido;
			const itens = pedido.itens.map(
				(item: any) =>
					new DetalhesItemPedido(
						item.descricao,
						item.quantidade,
						item.precoUnitario,
						item.precoTotal,
						item.sabores.map(
							(sabor: any) =>
								new DetalhesSaborEscolhido(
									sabor.nome,
									sabor.ingredientesRemovidos,
									sabor.ingredientesSubstitutos,
									sabor.adicionais.map((adicional: { nome: string; quantidade: number }) => new DetalhesAdicionalSabor(adicional.nome, adicional.quantidade)),
								),
						),
					),
			);
			const valorDescontoPromocao = pedido.promocoes.map((promocao: { valorDesconto: number }) => promocao.valorDesconto).reduce((total: number, valor: number) => total + valor, 0);
			const valorDescontoFidelidade = pedido.fidelidade?.valorDesconto ?? 0;
			const endereco = pedido.endereco ? `${pedido.endereco.logradouro}, ${pedido.endereco.numero} - ${pedido.endereco.bairro}, ${pedido.endereco.cidade}/${pedido.endereco.uf}` : null;

			return new DetalhesPedido(
				pedido.id,
				pedido.data,
				pedido.tipoAtendimento,
				pedido.status,
				pedido.nomeCliente,
				pedido.telefoneCliente,
				endereco,
				itens,
				pedido.subtotal,
				valorDescontoPromocao,
				valorDescontoFidelidade,
				pedido.taxaEntrega,
				pedido.valorTotal,
				pedido.nomeEntregador,
				pedido.dataSaida,
				pedido.dataChegada,
				pedido.motivoCancelamento,
				pedido.dataCancelamento,
			);
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao obter os detalhes do pedido.');
		}
	}

	async alterarStatus(idPedido: number, status: string): Promise<void> {
		try {
			await this.servicoHttp.patch(`${API}/pedidos/status`, { idPedido, status });
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao alterar o status do pedido.');
		}
	}

	async cancelarPedido(idPedido: number, motivo: string): Promise<void> {
		try {
			await this.servicoHttp.post(`${API}/pedidos/cancelamento`, { idPedido, motivo });
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao cancelar o pedido.');
		}
	}

	async registrarSaidaParaEntrega(idPedido: number, idEntregador: number): Promise<void> {
		try {
			await this.servicoHttp.post(`${API}/pedidos/saida-entrega`, { idPedido, idEntregador });
		} catch (error) {
			this.tratarErro(error, 'Ocorreu um erro ao registrar a saída para entrega do pedido.');
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
