import { VisaoLoginDom } from '../../src/autenticacao/view/visao-login-dom';

export default function login() {
	const visaoLogin = new VisaoLoginDom();
	visaoLogin.iniciar();
}
