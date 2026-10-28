import { Cliente } from '../../cliente/dominio/cliente';
import { Entregador } from '../../entregador/dominio/entregador';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';

export interface VisaoEtapaAtendimento {
	iniciar(controladora: ControladoraRegistroPedido): void;

	limparInputFiltroEAdicionarFoco(): void;

	obterFiltroCliente(): string;

	exibirClientes(clientes: Cliente[]): void;

	exibirErroAoObterClientes(mensagem: string): void;

	exibirEntregadores(entregadores: Entregador[]): void;

	exibirErroAoObterEntregadores(mensagem: string): void;
}
