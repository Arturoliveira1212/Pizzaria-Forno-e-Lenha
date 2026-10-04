Funcionalidade: Registrar Novo Pedido para Pizzaria.
    A fim de atender os clientes da pizzaria com rapidez e sem erros
    Como um atendente
    Eu quero registrar pedidos com pizzas, sobremesas e bebidas
    De modo a enviar o pedido corretamente para a cozinha

    Cenário: Busca de todas as pizzas no cardápio
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos" e filtro pela categoria "Pizzas"
        Então deve aparecer todas as 10 pizzas do cardápio

    Cenário: Busca de pizza existente no cardápio
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "Margherita"
        Então deve aparecer o card da pizza com nome, descrição, precos e tamanho

    Cenário: Busca de pizza inexistente no cardápio
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "Pizza inexistente"
        Então deve aparecer a mensagem de aviso "Nenhuma pizza encontrada"

    Cenário: Busca de todas as bebidas no cardápio
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos" e filtro pela categoria "Bebidas"
        Então deve aparecer todas as 3 bebidas do cardápio

    Cenário: Busca de bebida existente no cardápio
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Bebidas" e por "Coca"
        Então deve aparecer o card do produto com nome, descrição e preço

    Cenário: Busca de pizza inexistente no cardápio
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Bebidas" e por "Bebida inexistente"
        Então deve aparecer a mensagem de aviso "Nenhuma bebida encontrada"

    Cenário: Adição de pizza no pedido
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "2" e clico em adicionar
        Então deve aparecer no resumo lateral a pizza "Calabresa"

    Cenário: Adição de pizza com tamanho diferente do padrão
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "Catupiry" e seleciono o tamanho "Grande" e clico em adicionar
        Então deve aparecer no resumo lateral a pizza "Frango com Catupiry" no tamanho "Grande"

    Cenário: Adição da mesma pizza duas vezes
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Pizzas" e por "8" e clico em adicionar 2 vezes
        Então deve aparecer no resumo lateral a pizza "Napolitana" com quantidade "2"

    Cenário: Adição de sobremesa no pedido
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Sobremesas" e por "Pudim" e clico em adicionar
        Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite"

    Cenário: Adição da mesma sobremesa duas vezes
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Produtos", filtro pela categoria "Sobremesas" e por "5" e clico em adicionar 2 vezes
        Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite" com quantidade "2"

    Cenário: Seleção de tipo atendimento do tipo entrega:
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Atendimento" e seleciono o tipo atendimento "Entrega"
        Então a opção de tipo cliente "Consumidor Final" deve ficar desabilitada.

    Cenário: Seleção de tipo cliente Consumidor Final:
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Atendimento" e seleciono o tipo cliente "Consumidor Final"
        A seção de clientes desaparece e aparece a frase: Pedido sem cliente vinculado.

    Cenário: Seleção do tipo cliente Cliente Identificado:
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Atendimento" e seleciono o tipo cliente "Cliente"
        A seção de clientes aparece.

    Cenário: Alteração de quantidade
        Dado que estou na tela de registro de pedido com a pizza "Calabresa" adicionada
        Quando aumento a quantidade para "2"
        Então o resumo deve ficar com "Calabresa" e quantidade "2"

    Cenário: Remoção de item no resumo
        Dado que estou na tela de registro de pedido com a pizza "Calabresa" adicionada
        Quando cliclo em remover o item
        Então o resumo deve ficar vazio

    Cenário: Tentativa de finalizar pedido sem itens
        Dado que estou na tela de registro de pedido
        Quando acesso a etapa "Atendimento" e clico em "Registrar pedido"
        Então deve aparecer a mensagem de erro "Não é possível registrar um pedido sem itens."

    Cenário: Aplicação da taxa de entrega com produtos no pedido
        Dado que estou na tela de registro de pedido com a pizza "Calabresa" adicionada
        Quando acesso a etapa "Atendimento" e seleciono o tipo atendimento "Entrega"
        Então o resumo deve exibir a taxa de entrega de "R$ 10,00"

    Cenário: Aplicação do desconto de fidelidade para cliente elegível
        Dado que registrei duas pizzas para o cliente "Carlos Eduardo Lima"
        Quando inicio outro pedido, adiciono uma pizza e seleciono o cliente "Carlos Eduardo Lima"
        Então o resumo deve exibir um desconto de fidelidade

    Cenário: Aplicação do desconto de promoção para pizzas elegíveis
        Dado que existe uma promoção vigente para pizzas
        Quando adiciono a quantidade mínima de pizzas do tamanho promovido
        Então o resumo deve exibir um desconto de promoção
