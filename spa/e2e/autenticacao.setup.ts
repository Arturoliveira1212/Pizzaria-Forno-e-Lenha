import { test as setup } from '@playwright/test';
import { TelaLogin } from './pom/TelaLogin';

setup('Entrar no sistema como atendente', async ({ page }) => {
	const tela = new TelaLogin(page);
	await tela.abrir();
	await tela.entrar('98765432100', 'atendente123');
	await tela.deveEstarNaPaginaInicial();

	await page.context().storageState({ path: 'e2e/.auth/usuario.json' });
});
