Funcionalidade: Entrar no sistema
    A fim de utilizar o sistema com segurança
    Como um atendente
    Eu quero me autenticar com meu CPF e minha senha
    De modo a acessar as funcionalidades do sistema

    Contexto:
        Dado que não estou autenticado no sistema

        Cenário: Login com sucesso
            Dado que estou na tela de login
            Quando informo o CPF "987.654.321-00" e a senha "atendente123"
            Então devo ser redirecionado para a página inicial
            E devo ver o nome "Mariana Silva" no menu

        Cenário: Login com senha incorreta
            Dado que estou na tela de login
            Quando informo o CPF "98765432100" e a senha "senha-errada"
            Então deve aparecer a mensagem de erro "CPF ou senha incorretos."

        Cenário: Logout
            Dado que entrei no sistema com o CPF "98765432100" e a senha "atendente123"
            Quando clico em "Sair"
            Então devo ser redirecionado para a tela de login
            E o nome "Mariana Silva" deve deixar de ser exibido

        Cenário: Acesso a uma página sem estar autenticado
            Quando tento acessar a tela de registro de pedido
            Então devo ser redirecionado para a tela de login
