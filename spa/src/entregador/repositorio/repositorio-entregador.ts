import { Entregador } from '../dominio/entregador';

export interface RepositorioEntregador {
	obterEntregadores(): Promise<Entregador[]>;
}
