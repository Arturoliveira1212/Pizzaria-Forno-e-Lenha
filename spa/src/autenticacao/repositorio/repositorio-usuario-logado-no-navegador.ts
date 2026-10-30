import { decodeFromBase64, encodeToBase64 } from '../../util/base64';
import { UsuarioLogado } from '../dominio/usuario-logado';
import { RepositorioUsuarioLogado } from './repositorio-usuario-logado';

const CHAVE_USUARIO_LOGADO = 'usuario';

export class RepositorioUsuarioLogadoNoNavegador implements RepositorioUsuarioLogado {
	constructor(private readonly armazenamento: Storage = localStorage) {}

	salvar(usuario: UsuarioLogado): void {
		this.armazenamento.setItem(CHAVE_USUARIO_LOGADO, encodeToBase64(JSON.stringify(usuario)));
	}

	obter(): UsuarioLogado | null {
		const valor = this.armazenamento.getItem(CHAVE_USUARIO_LOGADO);
		if (valor === null) {
			return null;
		}

		try {
			const usuario = JSON.parse(decodeFromBase64(valor));

			return new UsuarioLogado(usuario.id, usuario.nome, usuario.papel);
		} catch {
			this.remover();
			return null;
		}
	}

	remover(): void {
		this.armazenamento.removeItem(CHAVE_USUARIO_LOGADO);
	}
}
