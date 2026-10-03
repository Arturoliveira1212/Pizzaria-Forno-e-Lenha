import { Cliente } from '../dominio/cliente';

export interface RepositorioCliente {
	obterClientes(identificador: string): Promise<Cliente[]>;
}
