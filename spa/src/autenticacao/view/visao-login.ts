export interface VisaoLogin {
	exibirErro(mensagem: string): void;

	limparErro(): void;

	desabilitarEnvio(): void;

	habilitarEnvio(): void;

	redirecionarParaHome(): void;
}
