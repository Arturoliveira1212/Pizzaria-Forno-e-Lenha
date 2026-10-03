import { RepositorioPizza } from '../../pizza/repositorio/repositorio-pizza';
import { RepositorioPedido } from '../repositorio/repositorio-pedido';

export class GestorPedido {
	constructor(private readonly repositorioPedido: RepositorioPedido) {}
}
