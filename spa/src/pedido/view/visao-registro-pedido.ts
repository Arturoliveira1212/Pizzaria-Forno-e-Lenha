import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { CategoriaCardapio } from '../dominio/categoria-cardapio';
import { EtapaPedido } from '../dominio/etapa-pedido';

export class VisaoRegistroPedido {
	private readonly controladoraRegistroPedido: ControladoraRegistroPedido;
	private readonly menuEtapas = document.querySelector<HTMLElement>('#etapas')!;
	private readonly menuCategoriasCardapio = document.querySelector<HTMLElement>('#categorias-cardapio')!;

	constructor() {
		this.controladoraRegistroPedido = new ControladoraRegistroPedido(this);
	}

	iniciar() {
		this.menuEtapas.addEventListener('click', (event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLElement>('[data-etapa]');
			if (!botao) {
				return;
			}

			const etapa = (botao?.dataset.etapa as EtapaPedido) ?? EtapaPedido.ATENDIMENTO;
			this.exibirEtapa(etapa);
		});

		this.menuCategoriasCardapio.addEventListener('click', (event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLElement>('[data-categoria]');
			if (!botao) {
				return;
			}

			const categoria = (botao?.dataset.categoria as CategoriaCardapio) ?? CategoriaCardapio.PIZZA;
			this.exibirMenuCategoriasCardapio(categoria);

			this.controladoraRegistroPedido.carregarCardapio(categoria);
		});
	}

	private exibirEtapa(etapa: EtapaPedido) {
		this.menuEtapas.querySelectorAll<HTMLElement>('[data-etapa]').forEach((btn) => {
			btn.classList.toggle('active', btn.dataset.etapa === etapa);
		});

		Object.values(EtapaPedido).forEach((valor) => {
			document.querySelector(`#etapa-${valor}`)?.classList.toggle('d-none', valor !== etapa);
		});
	}

	private exibirMenuCategoriasCardapio(categoria: CategoriaCardapio) {
		this.menuCategoriasCardapio.querySelectorAll<HTMLElement>('[data-categoria]').forEach((el) => {
			el.classList.toggle('active', el.dataset.categoria === categoria);
		});

		Object.values(CategoriaCardapio).forEach((valor) => {
			document.querySelector(`#categoria-${valor}`)?.classList.toggle('d-none', valor !== categoria);
		});
	}
}
