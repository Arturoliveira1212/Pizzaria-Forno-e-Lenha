import { PizzaParaCardapio } from '../../pizza/dominio/pizza-para-cardapio';
import { ProdutoSimples } from '../../produto-simples/dominio/produto-simples';
import { TipoProdutoSimples } from '../../produto-simples/dominio/tipo-produto-simples';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { FiltroCardapio } from './visao-registro-pedido';

export interface VisaoEtapaProdutos {
	iniciar(controladora: ControladoraRegistroPedido): void;

	limparInputFiltroEAdicionarFoco(): void;

	obterFiltroCardapio(): FiltroCardapio;

	exibirPizzasNoCardapio(pizzas: PizzaParaCardapio[]): void;

	exibirErroAoObterPizzasParaCardapio(mensagem: string): void;

	exibirProdutosSimplesNoCardapio(produtos: ProdutoSimples[], tipo: TipoProdutoSimples): void;

	exibirErroAoObterProdutosSimplesParaCardapio(mensagem: string, tipo: TipoProdutoSimples): void;
}
