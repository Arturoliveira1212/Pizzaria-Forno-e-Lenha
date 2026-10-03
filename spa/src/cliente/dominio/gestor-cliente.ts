import { RepositorioCliente } from '../repositorio/repositorio-cliente';
import { Cliente } from './cliente';

export class GestorCliente {
	constructor(private readonly repositorioCliente: RepositorioCliente) {}

	async obterClientes(identificador: string = ''): Promise<Cliente[]> {
		return this.repositorioCliente.obterClientes(identificador);
	}
}
