import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';

export class VisaoRegistroPedido {
	private readonly controladoraRegistroPedido: ControladoraRegistroPedido;

	constructor() {
		this.controladoraRegistroPedido = new ControladoraRegistroPedido(this);
	}

	iniciar() {}
}
