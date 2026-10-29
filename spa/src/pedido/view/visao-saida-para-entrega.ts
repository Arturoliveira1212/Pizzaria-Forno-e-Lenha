import { Entregador } from '../../entregador/dominio/entregador';

export interface VisaoSaidaParaEntrega {
	exibirEntregadores(entregadores: Entregador[]): void;

	exibirErroAoObterEntregadores(mensagem: string): void;
}
