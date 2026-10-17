# Pizzaria Forno & Lenha

> Projeto para avaliação da disciplina de Projeto Integrador de Sistemas.

## Autores

- Artur Alves de Oliveira.
- Guilherme Carvalho da Rosa.

OBS: Os comandos de instalação e execução devem ser rodados na raiz do projeto.

## Instalação

1. Criar e preencher `.env`:

```bash
cd api/ && cp .env.example .env
```

2. Instalar as dependências e construir o banco de dados:

````bash
pnpm setup
````

3. Instalar os navegadores que o Playwright utiliza no teste E2E:

````bash
cd spa/ && pnpm exec playwright install
````

## Execução

- Para levantar a aplicação:

````bash
pnpm start
````

- Para rodar os testes

````bash
pnpm test
````

## Referências Bibliográficas

Templates Bootstrap usados:
- https://getbootstrap.com/docs/5.3/components/navbar/
- https://getbootstrap.com/docs/5.3/components/navs-tabs/
- https://getbootstrap.com/docs/5.3/components/button-group/
- https://getbootstrap.com/docs/5.3/forms/input-group/
- https://getbootstrap.com/docs/5.3/components/card/
- https://getbootstrap.com/docs/5.3/components/buttons/
- https://getbootstrap.com/docs/5.3/components/list-group/
- https://getbootstrap.com/docs/5.3/components/modal/

Imagens dos produtos obtidas em:
- https://www.pngkey.com/
- https://www.pexels.com/pt-br/

Documentação de ferramentas extras usadas:
- https://github.com/php-cs-fixer/php-cs-fixer
- https://phinx.org/
- https://getbootstrap.com/
- https://github.com/visionmedia/page.js
