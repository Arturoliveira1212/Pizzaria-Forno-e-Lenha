import { API } from '../../infra/constantes';
import { HttpError } from '../../infra/exceptions/http-error';
import { RepositorioError } from '../../infra/exceptions/repositorio-error';
import { ServicoHttp } from '../../infra/http/servico-http';
import { Cliente } from '../dominio/cliente';
import { RepositorioCliente } from './repositorio-cliente';

export class RepositorioClienteNoServidor implements RepositorioCliente {
	constructor(private readonly servicoHttp: ServicoHttp) {}

	async obterClientes(identificador: string = ''): Promise<Cliente[]> {
		const clientes: Cliente[] = [];

		try {
			const parametros = new URLSearchParams();
			parametros.set('identificador', identificador);

			const resultado = await this.servicoHttp.get(`${API}/clientes?${parametros.toString()}`);

			resultado.data.clientes.forEach((cliente: any) => {
				clientes.push(new Cliente(cliente.id, cliente.nome, cliente.telefone, cliente.cpf));
			});
		} catch (error) {
			const mensagemErro = error instanceof HttpError ? error.data?.erros?.join(', ') : 'Ocorreu um erro ao obter os clientes.';
			throw new RepositorioError(mensagemErro);
		}

		return clientes;
	}
}
