import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { Pedido } from '../dominio/pedido';

export interface VisaoResumoPedido {
	iniciar(controladora: ControladoraRegistroPedido): void;

	exibirResumoPedido(pedido: Pedido): void;

	exibirErroNoPedido(mensagem: string): void;

	habilitarRegistroPedido(): void;

	desabilitarRegistroPedido(): void;

	exibirMensagemPedidoRegistrado(): void;
}
