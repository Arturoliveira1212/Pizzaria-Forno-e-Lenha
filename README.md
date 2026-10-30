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

Além dos dados de acesso aos bancos, o `.env` precisa da `PIMENTA`, um valor usado junto com a senha para gerar o hash das senhas dos usuários. Deve ser preenchida antes do passo 2, pois a semeadura do banco já grava as senhas com hash. Para gerar um valor aleatório:

````bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
````

````env
PIMENTA=valor
````

> Não altere o valor de pimenta após o cadastro dos usuários, pois deixaria de funcionar.

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

## Login

Todas as telas exigem login com CPF e senha. A semeadura do banco cria os seguintes usuários:

* Mariana Silva:  987.654.321-00 | atendente123
* Miguel Coves: 123.456.789-01 | admin123

A sessão expira após 15 minutos sem uso.

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
