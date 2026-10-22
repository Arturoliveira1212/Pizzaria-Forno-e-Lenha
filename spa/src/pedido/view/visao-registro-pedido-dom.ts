import { EtapaPedido, VisaoRegistroPedido } from './visao-registro-pedido';
import { VisaoEtapaProdutosDom } from './visao-etapa-produtos-dom';
import { VisaoPersonalizacaoPizzaDom } from './visao-personalizacao-pizza-dom';
import { VisaoEtapaAtendimentoDom } from './visao-etapa-atendimento-dom';
import { VisaoResumoPedidoDom } from './visao-resumo-pedido-dom';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import page from 'page';

export class VisaoRegistroPedidoDom implements VisaoRegistroPedido {
	private readonly visaoEtapaProdutos: VisaoEtapaProdutosDom;
	private readonly visaoPersonalizacaoPizza: VisaoPersonalizacaoPizzaDom;
	private readonly visaoEtapaAtendimento: VisaoEtapaAtendimentoDom;
	private readonly visaoResumoPedido: VisaoResumoPedidoDom;

	private readonly controladoraRegistroPedido: ControladoraRegistroPedido;
	private readonly menuEtapas = document.querySelector<HTMLElement>('#etapas')!;

	constructor() {
		this.visaoEtapaProdutos = new VisaoEtapaProdutosDom();
		this.visaoPersonalizacaoPizza = new VisaoPersonalizacaoPizzaDom();
		this.visaoEtapaAtendimento = new VisaoEtapaAtendimentoDom();
		this.visaoResumoPedido = new VisaoResumoPedidoDom();

		this.controladoraRegistroPedido = new ControladoraRegistroPedido({
			principal: this,
			produtos: this.visaoEtapaProdutos,
			personalizacao: this.visaoPersonalizacaoPizza,
			atendimento: this.visaoEtapaAtendimento,
			resumo: this.visaoResumoPedido,
		});
	}

	iniciar(): void {
		this.registrarEventos();
		this.visaoEtapaProdutos.iniciar(this.controladoraRegistroPedido);
		this.visaoPersonalizacaoPizza.iniciar(this.controladoraRegistroPedido);
		this.visaoEtapaAtendimento.iniciar(this.controladoraRegistroPedido);
		this.visaoResumoPedido.iniciar(this.controladoraRegistroPedido);
	}

	private registrarEventos(): void {
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

	private exibirEtapa(etapa: EtapaPedido): void {
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
