import { RepositorioPedido } from './repositorio-pedido';
import { ServicoHttp } from '../../infra/http/servico-http';

export class RepositorioPedidoNoServidor implements RepositorioPedido {
	constructor(private readonly servicoHttp: ServicoHttp) {}
}
