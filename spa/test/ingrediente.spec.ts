import { describe, expect, it } from 'vitest';
import { Ingrediente } from '../src/ingrediente/dominio/ingrediente';

describe('Ingrediente', () => {
	describe('temEstoqueSuficiente()', () => {
		it('retorna true quando a quantidade é menor que o estoque', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);

			expect(ingrediente.temEstoqueSuficiente(9)).toBeTruthy();
		});

		it('retorna true quando a quantidade é igual ao estoque', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);

			expect(ingrediente.temEstoqueSuficiente(10)).toBeTruthy();
		});

		it('retorna false quando a quantidade é maior que o estoque', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);

			expect(ingrediente.temEstoqueSuficiente(11)).toBeFalsy();
		});
	});
});
