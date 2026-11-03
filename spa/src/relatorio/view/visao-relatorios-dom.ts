import { Chart, registerables } from 'chart.js';
import { formatarDinheiro } from '../../util/dinheiro';
import { ControladoraRelatorios } from '../controller/controladora-relatorios';
import { DistribuicaoItem } from '../dominio/distribuicao-item';
import { TotalPedidosDia } from '../dominio/total-pedidos-dia';
import { VisaoRelatorios } from './visao-relatorios';

Chart.register(...registerables);

export class VisaoRelatoriosDom implements VisaoRelatorios {
	private readonly pagina = document.querySelector<HTMLElement>('#pagina-relatorios')!;
	private readonly formularioRelatorio = this.pagina.querySelector<HTMLFormElement>('#formulario-relatorio')!;
	private readonly selectTipoRelatorio = this.pagina.querySelector<HTMLSelectElement>('#tipo-relatorio')!;
	private readonly inputDataInicio = this.pagina.querySelector<HTMLInputElement>('#data-inicio')!;
	private readonly inputDataFim = this.pagina.querySelector<HTMLInputElement>('#data-fim')!;
	private readonly alertaErro = this.pagina.querySelector<HTMLElement>('#erro-relatorio')!;
	private readonly canvasGrafico = this.pagina.querySelector<HTMLCanvasElement>('#grafico-relatorio')!;
	private readonly controladoraRelatorios: ControladoraRelatorios;
	private grafico: Chart | null = null;

	constructor() {
		this.controladoraRelatorios = new ControladoraRelatorios(this);
	}

	iniciar(): void {
		const hoje = new Date();
		this.inputDataInicio.value = this.formatarDataParaCampo(new Date(hoje.getFullYear(), hoje.getMonth(), 1));
		this.inputDataFim.value = this.formatarDataParaCampo(new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0));

		this.formularioRelatorio.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.gerarRelatorio();
		});
		this.selectTipoRelatorio.addEventListener('change', () => this.gerarRelatorio());

		this.gerarRelatorio();
	}

	exibirTotalPedidosPorDia(totaisPorDia: TotalPedidosDia[]): void {
		this.exibirGrafico(
			'bar',
			totaisPorDia.map((total) => this.formatarDataParaExibicao(total.data)),
			totaisPorDia.map((total) => total.valorTotal),
		);
	}

	exibirDistribuicaoItens(itens: DistribuicaoItem[]): void {
		this.exibirGrafico(
			'pie',
			itens.map((item) => `${item.nome}: ${formatarDinheiro(item.valor)} (${item.percentual.toLocaleString('pt-BR')}%)`),
			itens.map((item) => item.valor),
		);
	}

	exibirErro(mensagem: string): void {
		this.alertaErro.textContent = mensagem;
		this.alertaErro.classList.remove('d-none');
	}

	private gerarRelatorio(): void {
		this.alertaErro.classList.add('d-none');

		if (this.selectTipoRelatorio.value === 'distribuicao-itens') {
			this.controladoraRelatorios.obterDistribuicaoItens(this.inputDataInicio.value, this.inputDataFim.value);
			return;
		}

		this.controladoraRelatorios.obterTotalPedidosPorDia(this.inputDataInicio.value, this.inputDataFim.value);
	}

	private exibirGrafico(tipo: 'bar' | 'pie', rotulos: string[], valores: number[]): void {
		this.grafico?.destroy();
		this.grafico = new Chart(this.canvasGrafico, {
			type: tipo,
			data: {
				labels: rotulos,
				datasets: [{ label: 'Valor vendido', data: valores }],
			},
		});
	}

	private formatarDataParaCampo(data: Date): string {
		const mes = String(data.getMonth() + 1).padStart(2, '0');
		const dia = String(data.getDate()).padStart(2, '0');

		return `${data.getFullYear()}-${mes}-${dia}`;
	}

	private formatarDataParaExibicao(data: string): string {
		const [ano, mes, dia] = data.split('-');

		return `${dia}/${mes}/${ano}`;
	}
}
