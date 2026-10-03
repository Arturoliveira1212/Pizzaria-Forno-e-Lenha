export enum TamanhoPizza {
	PEQUENA = 'PEQUENA',
	MEDIA = 'MEDIA',
	GRANDE = 'GRANDE',
}

export function rotuloTamanho(tamanho: TamanhoPizza): string {
	const rotulo = {
		[TamanhoPizza.PEQUENA]: 'P',
		[TamanhoPizza.MEDIA]: 'M',
		[TamanhoPizza.GRANDE]: 'G',
	};

	return rotulo[tamanho] || 'Desconhecido';
}
