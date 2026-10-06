import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import page from 'page';

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

page('/', async () => {
	await carregarConteudo('/app/pages/home.html');
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
