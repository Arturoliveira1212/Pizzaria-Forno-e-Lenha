import { DetalhesPedido } from '../dominio/detalhes-pedido';

export interface VisaoDetalhesPedido {
	exibirDetalhesPedido(detalhesPedido: DetalhesPedido): void;

	exibirErroAoObterDetalhesPedido(mensagem: string): void;
}
