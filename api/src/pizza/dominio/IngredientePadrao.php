<?php

class IngredientePadrao {
    public function __construct(
        public readonly int $id,
        public readonly Ingrediente $ingrediente,
        public readonly bool $removivel,
        public readonly bool $substituivel,
    ) {}
}
