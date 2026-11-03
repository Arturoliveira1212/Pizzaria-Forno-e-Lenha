export class VisaoRelatoriosDom {
	private readonly pagina = document.querySelector<HTMLElement>('#pagina-relatorios')!;
	private readonly formularioRelatorio = this.pagina.querySelector<HTMLFormElement>('#formulario-relatorio')!;
	private readonly inputDataInicio = this.pagina.querySelector<HTMLInputElement>('#data-inicio')!;
	private readonly inputDataFim = this.pagina.querySelector<HTMLInputElement>('#data-fim')!;

	iniciar(): void {
		const hoje = new Date();
		this.inputDataInicio.value = this.formatarDataParaCampo(new Date(hoje.getFullYear(), hoje.getMonth(), 1));
		this.inputDataFim.value = this.formatarDataParaCampo(new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0));

		this.formularioRelatorio.addEventListener('submit', (event: Event) => event.preventDefault());
	}

	private formatarDataParaCampo(data: Date): string {
		const mes = String(data.getMonth() + 1).padStart(2, '0');
		const dia = String(data.getDate()).padStart(2, '0');

		return `${data.getFullYear()}-${mes}-${dia}`;
	}
}
