import { describe, expect, it } from 'vitest';
import { IngredientePadrao } from '../src/pizza/dominio/ingrediente-padrao';
import { Ingrediente } from '../src/ingrediente/dominio/ingrediente';
import { QuantidadePorTamanho } from '../src/pizza/dominio/quantidade-por-tamanho';
import { TamanhoPizza } from '../src/pizza/dominio/tamanho-pizza';

describe('IngredientePadrao', () => {
	describe('ehFixo()', () => {
		it('retorna true quando o ingrediente é fixo', () => {
			const ingredientePadrao = new IngredientePadrao(1, new Ingrediente(1, 'Ingrediente 1', 10), false, false, []);

			expect(ingredientePadrao.ehFixo()).toBeTruthy();
		});

		it('retorna false quando o ingrediente não é fixo (apenas removível)', () => {
			const ingredientePadrao = new IngredientePadrao(1, new Ingrediente(1, 'Ingrediente 1', 10), true, false, []);

			expect(ingredientePadrao.ehFixo()).toBeFalsy();
		});

		it('retorna false quando o ingrediente não é fixo (apenas substituível)', () => {
			const ingredientePadrao = new IngredientePadrao(1, new Ingrediente(1, 'Ingrediente 1', 10), false, true, []);

			expect(ingredientePadrao.ehFixo()).toBeFalsy();
		});

		it('retorna false quando o ingrediente não é fixo (removível e substituível)', () => {
			const ingredientePadrao = new IngredientePadrao(1, new Ingrediente(1, 'Ingrediente 1', 10), true, true, []);

			expect(ingredientePadrao.ehFixo()).toBeFalsy();
		});
	});

	describe('temEstoqueSuficiente()', () => {
		it('retorna true quando a quantidade para o tamanho da pizza é menor que o estoque', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);
			const ingredientePadrao = new IngredientePadrao(1, ingrediente, false, false, [new QuantidadePorTamanho(1, TamanhoPizza.PEQUENA, 9)]);

			expect(ingredientePadrao.temEstoqueSuficiente(TamanhoPizza.PEQUENA)).toBeTruthy();
		});

		it('retorna true quando a quantidade para o tamanho da pizza é igual ao estoque', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);
			const ingredientePadrao = new IngredientePadrao(1, ingrediente, false, false, [new QuantidadePorTamanho(1, TamanhoPizza.PEQUENA, 10)]);

			expect(ingredientePadrao.temEstoqueSuficiente(TamanhoPizza.PEQUENA)).toBeTruthy();
		});

		it('retorna false quando a quantidade para o tamanho da pizza é maior que o estoque', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);
			const ingredientePadrao = new IngredientePadrao(1, ingrediente, false, false, [new QuantidadePorTamanho(1, TamanhoPizza.PEQUENA, 15)]);

			expect(ingredientePadrao.temEstoqueSuficiente(TamanhoPizza.PEQUENA)).toBeFalsy();
		});

		it('retorna false quando não há quantidade definida para o tamanho da pizza', () => {
			const ingrediente = new Ingrediente(1, 'Ingrediente 1', 10);
			const ingredientePadrao = new IngredientePadrao(1, ingrediente, false, false, []);

			expect(ingredientePadrao.temEstoqueSuficiente(TamanhoPizza.PEQUENA)).toBeFalsy();
		});
	});
});
