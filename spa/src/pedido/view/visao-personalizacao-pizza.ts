import { Pizza } from '../../pizza/dominio/pizza';
import { TamanhoPizza } from '../../pizza/dominio/tamanho-pizza';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { DadosItemPizza } from './visao-registro-pedido';

export interface VisaoPersonalizacaoPizza {
	iniciar(controladora: ControladoraRegistroPedido): void;

	exibirPizzaParaPersonalizacao(pizza: Pizza, tamanho: TamanhoPizza): void;

	abrirModal(): void;

	fecharModal(): void;

	obterFiltroSegundoSabor(): string;

	obterIdPizzaPrimeiroSabor(): number;

	exibirPizzasParaSegundoSabor(pizzas: Pizza[]): void;

	exibirErroAoObterPizzasParaSegundoSabor(mensagem: string): void;

	adicionarPizzaComoSegundoSabor(pizza: Pizza): void;

	obterDadosPersonalizacaoItemPizza(): DadosItemPizza;
}
