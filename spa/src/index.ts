import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import page from 'page';
import { VisaoMenuUsuarioDom } from './autenticacao/view/visao-menu-usuario-dom';
import { EVENTO_SESSAO_EXPIRADA } from './infra/http/servico-http-com-fetch';

async function carregarConteudo(arquivo: string, script?: () => void): Promise<void> {
	const main = document.querySelector<HTMLElement>('main');
	if (!main) {
		console.error('Elemento não encontrado.');
		return;
	}

	try {
		const response: Response = await fetch(arquivo);
		if (!response.ok) {
			throw new Error(`Erro ao carregar ${arquivo}`);
		}
		const html = await response.text();
		main.innerHTML = html;

		if (script) {
			script();
		}
	} catch (error) {
		main.innerHTML = 'Não foi possível carregar o conteúdo.';
	}
}

const visaoMenuUsuario = new VisaoMenuUsuarioDom();
visaoMenuUsuario.iniciar();

window.addEventListener(EVENTO_SESSAO_EXPIRADA, () => visaoMenuUsuario.encerrarSessaoExpirada());

page('*', (ctx, next) => {
	const usuarioLogado = visaoMenuUsuario.verificarUsuarioLogado();

	if (ctx.path === '/login' && usuarioLogado) {
		page.redirect('/');
		return;
	}

	if (ctx.path !== '/login' && !usuarioLogado) {
		page.redirect('/login');
		return;
	}

	next();
});

page('/login', async () => {
	await carregarConteudo('/app/pages/login.html', (await import('../app/js/login.js')).default);
});

page('/', async () => {
	await carregarConteudo('/app/pages/monitor-pedidos.html', (await import('../app/js/monitor-pedidos.js')).default);
});

page.exit('/', async (ctx, next) => {
	(await import('../app/js/monitor-pedidos.js')).pararMonitorPedidos();
	next();
});

page.redirect('/home', '/');

page('/pedido/registrar', async () => {
	await carregarConteudo('/app/pages/registrar-pedido.html', (await import('../app/js/registro-pedido.js')).default);
});

page('/pedido', async () => {
	await carregarConteudo('/app/pages/listagem-pedidos.html', (await import('../app/js/listagem-pedidos.js')).default);
});

page('*', async () => {
	await carregarConteudo('/app/pages/404.html');
});

page();
