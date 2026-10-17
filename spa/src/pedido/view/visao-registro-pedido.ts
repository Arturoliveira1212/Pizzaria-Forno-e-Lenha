import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { EtapaPedido } from '../etapa-pedido';
import { VisaoEtapaProdutos } from './visao-etapa-produtos';
import { VisaoEtapaAtendimento } from './visao-etapa-atendimento';
import { VisaoResumoPedido } from './visao-resumo-pedido';
import { VisaoPersonalizacaoPizza } from './visao-personalizacao-pizza';
import page from 'page';

export class VisaoRegistroPedido {
	private readonly visaoEtapaProdutos: VisaoEtapaProdutos;
	private readonly visaoPersonalizacaoPizza: VisaoPersonalizacaoPizza;
	private readonly visaoEtapaAtendimento: VisaoEtapaAtendimento;
	private readonly visaoResumoPedido: VisaoResumoPedido;

	private readonly controladoraRegistroPedido: ControladoraRegistroPedido;
	private readonly menuEtapas = document.querySelector<HTMLElement>('#etapas')!;

	constructor() {
		this.visaoEtapaProdutos = new VisaoEtapaProdutos();
		this.visaoPersonalizacaoPizza = new VisaoPersonalizacaoPizza();
		this.visaoEtapaAtendimento = new VisaoEtapaAtendimento();
		this.visaoResumoPedido = new VisaoResumoPedido();

		this.controladoraRegistroPedido = new ControladoraRegistroPedido({
			principal: this,
			produtos: this.visaoEtapaProdutos,
			personalizacao: this.visaoPersonalizacaoPizza,
			atendimento: this.visaoEtapaAtendimento,
			resumo: this.visaoResumoPedido,
		});
	}

	iniciar() {
		this.registrarEventos();
		this.visaoEtapaProdutos.iniciar(this.controladoraRegistroPedido);
		this.visaoPersonalizacaoPizza.iniciar(this.controladoraRegistroPedido);
		this.visaoEtapaAtendimento.iniciar(this.controladoraRegistroPedido);
		this.visaoResumoPedido.iniciar(this.controladoraRegistroPedido);
	}

	private registrarEventos() {
		this.menuEtapas.addEventListener('click', (event: Event) => {
			const elementoClicado = event.target as HTMLElement;
			const botao = elementoClicado.closest<HTMLElement>('[data-etapa]');
			if (!botao) {
				return;
			}

			const etapa = (botao?.dataset.etapa as EtapaPedido) ?? EtapaPedido.ATENDIMENTO;
			this.exibirEtapa(etapa);
		});
	}

	private exibirEtapa(etapa: EtapaPedido) {
		this.menuEtapas.querySelectorAll<HTMLElement>('[data-etapa]').forEach((btn) => {
			btn.classList.toggle('active', btn.dataset.etapa === etapa);
		});

		Object.values(EtapaPedido).forEach((valor) => {
			document.querySelector(`#etapa-${valor}`)?.classList.toggle('d-none', valor !== etapa);
		});

		if (etapa === EtapaPedido.PRODUTOS) {
			this.visaoEtapaProdutos.limparInputFiltroEAdicionarFoco();
		} else if (etapa === EtapaPedido.ATENDIMENTO) {
			this.visaoEtapaAtendimento.limparInputFiltroEAdicionarFoco();
		}

		etapa !== EtapaPedido.ATENDIMENTO ? this.visaoResumoPedido.desabilitarRegistroPedido() : this.visaoResumoPedido.habilitarRegistroPedido();
	}

	redirecionarParaHome(): void {
		window.setTimeout(() => {
			page('/home');
		}, 1000);
	}
}
