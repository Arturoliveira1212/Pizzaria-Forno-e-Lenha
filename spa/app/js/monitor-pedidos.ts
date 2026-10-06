import { VisaoMonitorPedidos } from '../../src/pedido/view/visao-monitor-pedidos';

let visaoMonitorPedidos: VisaoMonitorPedidos | null = null;

export default function monitorPedidos() {
	visaoMonitorPedidos = new VisaoMonitorPedidos();
	visaoMonitorPedidos.iniciar();
}

export function pararMonitorPedidos(): void {
	visaoMonitorPedidos?.parar();
}
