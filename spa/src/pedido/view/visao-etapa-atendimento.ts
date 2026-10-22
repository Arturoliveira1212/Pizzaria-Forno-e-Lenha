import { Cliente } from '../../cliente/dominio/cliente';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';

export interface VisaoEtapaAtendimento {
	iniciar(controladora: ControladoraRegistroPedido): void;

	limparInputFiltroEAdicionarFoco(): void;

	obterFiltroCliente(): string;

	exibirClientes(clientes: Cliente[]): void;

	exibirErroAoObterClientes(mensagem: string): void;
}
