export function clonarTemplate(id: string): DocumentFragment {
	const template = document.querySelector<HTMLTemplateElement>(`#${id}`);
	if (!template) {
		throw new Error(`Template com id "${id}" não encontrado.`);
	}

	return template.content.cloneNode(true) as DocumentFragment;
}

export function limparInputEAdicionarFoco(input: HTMLInputElement): void {
	input.value = '';
	input.focus();
}
