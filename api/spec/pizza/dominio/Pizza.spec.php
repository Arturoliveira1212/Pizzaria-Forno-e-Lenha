<?php

describe('Pizza', function () {

    $this->molho = new Ingrediente(1, 'Molho');
    $this->mussarela = new Ingrediente(2, 'Mussarela');
    $this->cebola = new Ingrediente(3, 'Cebola');
    $this->milho = new Ingrediente(4, 'Milho');
    $this->bacon = new Ingrediente(5, 'Bacon');
    $this->catupiry = new Ingrediente(6, 'Catupiry');
    $this->manjericao = new Ingrediente(7, 'Manjericão');
    $this->calabresa = new Ingrediente(8, 'Calabresa');

    $this->pizza = new Pizza(
        id: 1,
        nome: 'Marguerita',
        descricao: 'Descrição',
        ingredientesPadrao: [
            new IngredientePadrao(id: 1, ingrediente: $this->molho, removivel: false, substituivel: false),
            new IngredientePadrao(id: 2, ingrediente: $this->mussarela, removivel: false, substituivel: false),
            new IngredientePadrao(id: 3, ingrediente: $this->manjericao, removivel: true, substituivel: true),
        ],
        ingredientesSubstitutos: [$this->cebola, $this->milho],
        adicionais: [
            new IngredienteAdicional(id: 1, ingrediente: $this->bacon, preco: new Dinheiro(599)),
            new IngredienteAdicional(id: 2, ingrediente: $this->catupiry, preco: new Dinheiro(799)),
            new IngredienteAdicional(id: 3, ingrediente: $this->calabresa, preco: new Dinheiro(699)),
        ],
        precosPorTamanho: [
            new PrecoPorTamanho(id: 1, tamanhoPizza: TamanhoPizza::PEQUENA, preco: new Dinheiro(1999)),
            new PrecoPorTamanho(id: 2, tamanhoPizza: TamanhoPizza::MEDIA, preco: new Dinheiro(2499)),
        ],
    );

    describe('obterAdicional()', function () {

        it('retorna o adicional quando ele existe', function () {
            $adicional = $this->pizza->obterAdicional($this->bacon);

            expect($adicional)->toBeAnInstanceOf(IngredienteAdicional::class);
            expect($adicional->ingrediente->id)->toBe($this->bacon->id);
        });

        it('retorna null quando o adicional não existe', function () {
            $adicional = $this->pizza->obterAdicional($this->cebola);

            expect($adicional)->toBeNull();
        });

    });

    describe('temComoIngredientePadrao()', function () {

        it('retorna true quando o ingrediente é padrão', function () {
            expect($this->pizza->temComoIngredientePadrao($this->molho))->toBe(true);
        });

        it('retorna false quando o ingrediente não é padrão', function () {
            expect($this->pizza->temComoIngredientePadrao($this->cebola))->toBe(false);
        });

    });

    describe('temComoIngredienteSubstituto()', function () {

        it('retorna true quando o ingrediente é substituto', function () {
            expect($this->pizza->temComoIngredienteSubstituto($this->cebola))->toBe(true);
        });

        it('retorna false quando o ingrediente não é substituto', function () {
            expect($this->pizza->temComoIngredienteSubstituto($this->molho))->toBe(false);
        });

    });

    describe('obterIdsIngredientesPadraoNaoPersonalizaveis()', function () {

        it('retorna os ids dos ingredientes padrão que não são personalizáveis', function () {
            $idsIngredientesNaoPersonalizaveis = $this->pizza->obterIdsIngredientesPadraoNaoPersonalizaveis();

            expect($idsIngredientesNaoPersonalizaveis)->toHaveLength(2);
            expect($idsIngredientesNaoPersonalizaveis)->toContain($this->molho->id);
            expect($idsIngredientesNaoPersonalizaveis)->toContain($this->mussarela->id);
        });

    });

    describe('obterPrecoPorTamanho()', function () {

        it('retorna o preço quando ele existe', function () {
            $preco = $this->pizza->obterPrecoPorTamanho(TamanhoPizza::MEDIA);

            expect($preco->centavos)->toBe(2499);
        });

        it('retorna null quando o preço não existe', function () {
            $preco = $this->pizza->obterPrecoPorTamanho(TamanhoPizza::GRANDE);

            expect($preco)->toBeNull();
        });

    });

    describe('temTamanho()', function () {

        it('retorna true quando o tamanho existe', function () {
            expect($this->pizza->temTamanho(TamanhoPizza::MEDIA))->toBe(true);
        });

        it('retorna false quando o tamanho não existe', function () {
            expect($this->pizza->temTamanho(TamanhoPizza::GRANDE))->toBe(false);
        });

    });

});
