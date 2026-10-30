import { test } from '@playwright/test';
import { TelaLogin } from './pom/TelaLogin';

test.describe('Login', () => {
	let tela: TelaLogin;

	test.use({ storageState: { cookies: [], origins: [] } });

	test.beforeEach(async ({ page }) => {
		tela = new TelaLogin(page);
	});

	/*
		Funcionalidade: Entrar no sistema
			A fim de utilizar o sistema com segurança
			Como um atendente
			Eu quero me autenticar com meu CPF e minha senha
			De modo a acessar as funcionalidades do sistema
	*/
	test.describe('Entrar no sistema', () => {
		test.describe('Dado que não estou autenticado no sistema', () => {
			/*
				Cenário: Login com sucesso
					Dado que estou na tela de login
					Quando informo o CPF "987.654.321-00" e a senha "atendente123"
					Então devo ser redirecionado para a página inicial
					E devo ver o nome "Mariana Silva" no menu
			*/
			test('Login com sucesso', async () => {
				await tela.abrir();
				await tela.entrar('987.654.321-00', 'atendente123');
				await tela.deveEstarNaPaginaInicial();
				await tela.deveExibirNomeDoUsuario('Mariana Silva');
			});

			/*
				Cenário: Login com senha incorreta
					Dado que estou na tela de login
					Quando informo o CPF "98765432100" e a senha "senha-errada"
					Então deve aparecer a mensagem de erro "CPF ou senha incorretos."
			*/
			test('Login com senha incorreta', async () => {
				await tela.abrir();
				await tela.entrar('98765432100', 'senha-errada');
				await tela.deveExibirErro('CPF ou senha incorretos.');
			});

			/*
				Cenário: Logout
					Dado que entrei no sistema com o CPF "98765432100" e a senha "atendente123"
					Quando clico em "Sair"
					Então devo ser redirecionado para a tela de login
					E o nome "Mariana Silva" deve deixar de ser exibido
			*/
			test('Logout', async () => {
				await tela.abrir();
				await tela.entrar('98765432100', 'atendente123');
				await tela.deveExibirNomeDoUsuario('Mariana Silva');
				await tela.sair();
				await tela.deveEstarNaTelaDeLogin();
				await tela.deveNaoExibirNomeDoUsuario();
			});

			/*
				Cenário: Acesso a uma página sem estar autenticado
					Quando tento acessar a tela de registro de pedido
					Então devo ser redirecionado para a tela de login
			*/
			test('Acesso a uma página sem estar autenticado', async () => {
				await tela.abrirPagina('/pedido/registrar');
				await tela.deveEstarNaTelaDeLogin();
			});
		});
	});
});
