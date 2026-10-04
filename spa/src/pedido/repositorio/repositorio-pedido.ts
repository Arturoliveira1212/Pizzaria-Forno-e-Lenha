import { Pedido } from '../dominio/pedido';
import { ResumoDescontosPedido } from '../dominio/resumo-descontos-pedido';

export interface RepositorioPedido {
	obterResumoDescontos(Pedido: Pedido): Promise<ResumoDescontosPedido>;
}
