import { VisaoEtapaAtendimento } from './visao-etapa-atendimento';
import { Cliente } from '../../cliente/dominio/cliente';
import { clonarTemplate } from '../../util/dom';
import { ControladoraRegistroPedido } from '../controller/controladora-registro-pedido';
import { TipoAtendimento } from '../dominio/tipo-atendimento';
import { limparInputEAdicionarFoco } from '../../util/dom';
import { TipoCliente } from './visao-registro-pedido';

export class VisaoEtapaAtendimentoDom implements VisaoEtapaAtendimento {
	private controladoraRegistroPedido!: ControladoraRegistroPedido;

	private readonly menuTiposAtendimento = document.querySelector<HTMLElement>('#tipos-atendimento')!;
	private readonly menuTiposCliente = document.querySelector<HTMLElement>('#tipos-cliente')!;
	private readonly secaoCliente = document.querySelector<HTMLElement>('#secao-cliente')!;
	private readonly formularioFiltroCliente = this.secaoCliente.querySelector<HTMLElement>('#formulario-filtro-cliente')!;
	private readonly inputFiltroCliente = this.secaoCliente.querySelector<HTMLInputElement>('#filtro-cliente')!;
	private readonly secaoConsumidorFinal = document.querySelector<HTMLElement>('#secao-consumidor-final')!;
	private readonly listaClientes = document.querySelector<HTMLElement>('#lista-clientes')!;

	iniciar(controladora: ControladoraRegistroPedido): void {
		this.controladoraRegistroPedido = controladora;

		this.registrarEventos();
		this.limparInputFiltroEAdicionarFoco();
	}

	private registrarEventos(): void {
		this.menuTiposAtendimento.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'tipo-atendimento' || !radio.checked) {
				return;
			}

			this.atualizarRestricoesTipoAtendimento();
			this.limparInputFiltroEAdicionarFoco();
			this.controladoraRegistroPedido.atualizarTipoAtendimento(radio.value as TipoAtendimento);
		});

		this.menuTiposCliente.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'tipo-cliente' || !radio.checked) {
				return;
			}

			const tipoCliente = (radio.value as TipoCliente) || TipoCliente.CLIENTE_IDENTIFICADO;

			this.atualizarSecaoCliente(tipoCliente);
			this.atualizarRestricoesTipoAtendimento();
			this.limparInputFiltroEAdicionarFoco();
		});

		this.formularioFiltroCliente.addEventListener('submit', (event: Event) => {
			event.preventDefault();
			this.controladoraRegistroPedido.obterClientes();
		});

		this.listaClientes.addEventListener('change', (event: Event) => {
			const radio = event.target as HTMLInputElement;
			if (radio.name !== 'cliente' || !radio.checked) {
				return;
			}

			const idCliente = Number(radio.value);
			this.controladoraRegistroPedido.adicionarCliente(idCliente);
		});
	}

	private atualizarRestricoesTipoAtendimento(): void {
		const tipoConsumidorFinal = this.menuTiposCliente.querySelector<HTMLInputElement>('#tipo-consumidor-final')!;
		const tipoClienteIdentificado = this.menuTiposCliente.querySelector<HTMLInputElement>('#tipo-cliente-identificado')!;
		const tipoEntrega = this.menuTiposAtendimento.querySelector<HTMLInputElement>(`input[name="tipo-atendimento"][value="${TipoAtendimento.ENTREGA}"]`)!;

		tipoConsumidorFinal.disabled = tipoEntrega.checked;
		tipoEntrega.disabled = tipoConsumidorFinal.checked;

		if (tipoEntrega.checked && tipoConsumidorFinal.checked) {
			tipoClienteIdentificado.checked = true;
			tipoConsumidorFinal.checked = false;
			this.atualizarSecaoCliente(TipoCliente.CLIENTE_IDENTIFICADO);
		}
	}

	private atualizarSecaoCliente(tipoCliente: TipoCliente): void {
		const ehConsumidorFinal = tipoCliente === TipoCliente.CONSUMIDOR_FINAL;
		this.secaoCliente.classList.toggle('d-none', ehConsumidorFinal);
		this.secaoConsumidorFinal.classList.toggle('d-none', !ehConsumidorFinal);

		if (tipoCliente === TipoCliente.CONSUMIDOR_FINAL) {
			this.controladoraRegistroPedido.removerCliente();
		} else {
			this.listaClientes.textContent = '';
		}
	}

	limparInputFiltroEAdicionarFoco(): void {
		limparInputEAdicionarFoco(this.inputFiltroCliente);
	}

	obterFiltroCliente(): string {
		return this.inputFiltroCliente.value.trim();
	}

	exibirClientes(clientes: Cliente[]): void {
		this.listaClientes.replaceChildren();

		if (clientes.length === 0) {
			this.listaClientes.appendChild(this.criarAvisoCliente('Nenhum cliente encontrado.'));
			return;
		}

		const inputsClientes = clientes.map((cliente) => this.criarInputCliente(cliente));
		this.listaClientes.append(...inputsClientes);
	}

	exibirErroAoObterClientes(mensagem: string): void {
		this.listaClientes.replaceChildren(this.criarAvisoCliente(mensagem));
	}

	private criarAvisoCliente(mensagem: string): DocumentFragment {
		const template = clonarTemplate('template-cliente-erro');
		template.querySelector<HTMLElement>('.mensagem')!.textContent = mensagem;

		return template;
	}

	private criarInputCliente(cliente: Cliente): DocumentFragment {
		const template = clonarTemplate('template-cliente');

		const input = template.querySelector<HTMLInputElement>('input[type="radio"]')!;
		input.id = `cliente-${cliente.id}`;
		input.name = 'cliente';
		input.value = String(cliente.id);
		input.dataset.nome = cliente.nome;
		input.dataset.telefone = cliente.telefone;
		input.dataset.cpf = cliente.cpf;

		const label = template.querySelector<HTMLLabelElement>('label')!;
		const span = label.querySelector<HTMLSpanElement>('span')!;
		span.textContent = `(${cliente.cpf}) - ${cliente.nome}`;

		const small = label.querySelector<HTMLSpanElement>('small')!;
		small.textContent = cliente.telefone;

		return template;
	}
}
