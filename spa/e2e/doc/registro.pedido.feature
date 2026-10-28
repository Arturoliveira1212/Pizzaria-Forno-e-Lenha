Funcionalidade: Buscar pizzas no cardápio
    A fim de encontrar rapidamente a pizza desejada pelo cliente
    Como um atendente
    Eu quero filtrar e pesquisar pizzas no cardápio
    De modo a adicioná-las ao pedido

    Contexto:
        Dado que estou na tela de registro de pedido
        E acesso a etapa "Produtos"

        Cenário: Busca de todas as pizzas no cardápio
            Quando filtro pela categoria "Pizzas"
            Então deve aparecer todas as 10 pizzas do cardápio

        Cenário: Busca de pizza existente no cardápio
            Quando filtro pela categoria "Pizzas" e pesquiso por "Quatro Queijos"
            Então deve aparecer o card da pizza "Quatro Queijos" com nome, descrição, preços e tamanho

        Cenário: Busca de pizza inexistente no cardápio
            Quando filtro pela categoria "Pizzas" e pesquiso por "Pizza inexistente"
            Então deve aparecer a mensagem de aviso "Nenhuma pizza encontrada"

Funcionalidade: Buscar bebidas no cardápio
    A fim de encontrar rapidamente a bebida desejada pelo cliente
    Como um atendente
    Eu quero filtrar e pesquisar bebidas no cardápio
    De modo a adicioná-las ao pedido

    Contexto:
        Dado que estou na tela de registro de pedido
        E acesso a etapa "Produtos"

        Cenário: Busca de todas as bebidas no cardápio
            Quando filtro pela categoria "Bebidas"
            Então deve aparecer todas as 3 bebidas do cardápio

        Cenário: Busca de bebida existente no cardápio
            Quando filtro pela categoria "Bebidas" e pesquiso por "Coca"
            Então deve aparecer o card da bebida "Coca-Cola 2L" com nome, descrição e preço

        Cenário: Busca de bebida inexistente no cardápio
            Quando filtro pela categoria "Bebidas" e pesquiso por "Bebida inexistente"
            Então deve aparecer a mensagem de aviso "Nenhuma bebida encontrada"

Funcionalidade: Gerenciar itens do pedido
    A fim de montar o pedido do cliente sem erros
    Como um atendente
    Eu quero adicionar, alterar e remover pizzas e sobremesas no pedido
    De modo a enviar o pedido correto para a cozinha

    Contexto:
        Dado que estou na tela de registro de pedido
        E acesso a etapa "Produtos"

        Cenário: Adição de pizza no pedido
            Quando filtro pela categoria "Pizzas" e pesquiso por ID "2"
            E clico em adicionar a pizza "Calabresa"
            Então deve aparecer no resumo lateral a pizza "Calabresa"

        Cenário: Adição de pizza com tamanho diferente do padrão
            Quando filtro pela categoria "Pizzas" e pesquiso por "Catupiry"
            E seleciono o tamanho "Grande" para a pizza "Frango com Catupiry"
            E clico em adicionar a pizza "Frango com Catupiry"
            Então deve aparecer no resumo lateral a pizza "Frango com Catupiry" no tamanho "Grande"

        Cenário: Adição da mesma pizza duas vezes
            Quando filtro pela categoria "Pizzas" e pesquiso por ID "8"
            E clico em adicionar a pizza "Napolitana" 2 vezes
            Então deve aparecer no resumo lateral a pizza "Napolitana" com quantidade "2"

        Cenário: Adição de sobremesa no pedido
            Quando filtro pela categoria "Sobremesas" e pesquiso por "Pudim"
            E clico em adicionar a sobremesa "Pudim de Leite"
            Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite"

        Cenário: Adição da mesma sobremesa duas vezes
            Quando filtro pela categoria "Sobremesas" e pesquiso por ID "5"
            E clico em adicionar a sobremesa "Pudim de Leite" 2 vezes
            Então deve aparecer no resumo lateral a sobremesa "Pudim de Leite" com quantidade "2"

        Cenário: Aumento da quantidade de um item
            Dado que a pizza "Calabresa" foi adicionada ao pedido 1 vez
            Quando aumento a quantidade de "Calabresa" para "2"
            Então o resumo deve ficar com "Calabresa" e quantidade "2"

        Cenário: Diminuição da quantidade de um item
            Dado que a pizza "Portuguesa" foi adicionada ao pedido 3 vezes
            Quando diminuo a quantidade de "Portuguesa" para "2"
            Então o resumo deve ficar com "Portuguesa" e quantidade "2"

        Cenário: Remoção de item no resumo
            Dado que a pizza "Calabresa" foi adicionada ao pedido
            Quando clico em remover o item "Calabresa"
            Então o resumo deve ficar vazio

Funcionalidade: Personalizar ingredientes da pizza
    A fim de atender às preferências do cliente
    Como um atendente
    Eu quero personalizar pizzas com adicionais, além da substituição e remoção de ingredientes
    De modo a enviar o pedido correto para a cozinha

    Contexto:
        Dado que estou na tela de registro de pedido
        E acesso a etapa "Produtos"

        Cenário: Abertura do modal de personalização de pizza
            Quando filtro pela categoria "Pizzas" e pesquiso por "Vegetariana"
            E seleciono o tamanho "Média" para a pizza "Vegetariana"
            E clico em personalizar a pizza "Vegetariana"
            Então o modal de personalização da pizza "Vegetariana" deve ser exibido

        Cenário: Fechmento do modal de personalização de pizza
            Dado que o modal de personalização da pizza "Vegetariana" no tamanho "Pequena" está aberto
            Quando clico em fechar o modal
            Então o modal de personalização da pizza "Vegetariana" deve ser fechado

        Cenário: Carregamento dos ingredientes da pizza no modal
            Dado que o modal de personalização da pizza "Napolitana" no tamanho "Grande" está aberto
            Então devem ser exibidos os ingredientes padrão da pizza "Napolitana"
            E devem ser ser exibidos os ingredientes substitutos disponíveis para a pizza "Napolitana"
            E devem ser exibidos os adicionais disponíveis para a pizza "Napolitana"
            E deve ser exibido o valor total da personalização da pizza "Napolitana"

        Cenário: Tentativa de remoção de ingrediente não permitido
            Dado que o modal de personalização da pizza "Napolitana" no tamanho "Grande" está aberto
            E a pizza "Napolitana" possui o ingrediente "Mussarela" como obrigatório
            Então o checkbox para remoção correspondente a "Mussarela" deve estar desabilitado

        Cenário: Remoção de ingrediente permitido
            Dado que o modal de personalização da pizza "Napolitana" no tamanho "Grande" está aberto
            E a pizza "Napolitana" possui o ingrediente "Tomate" como removível
            Quando removo o ingrediente "Tomate"
            Então o checkbox para remoção correspondente a "Tomate" deve estar marcado

        Cenário: Adição de ingrediente adicional
            Dado que o modal de personalização da pizza "Napolitana" no tamanho "Média" está aberto
            E a pizza "Napolitana" possui o ingrediente "Bacon" como adicional
            Quando aumento a quantidade do "Bacon" para "1"
            Então o valor do adicional "Bacon" deve ser adicionado ao valor total da personalização da pizza "Napolitana"

        Cenário: Remoção de ingrediente adicional
            Dado que o modal de personalização da pizza "Napolitana" no tamanho "Pequena" está aberto
            E a pizza "Napolitana" possui o ingrediente "Bacon" como adicional
            E a quantidade do "Bacon" está em "1"
            Quando diminuo a quantidade do "Bacon" para "0"
            Então o valor do adicional "Bacon" deve ser removido do valor total da personalização da pizza "Napolitana"

        Cenário: Adição de pizza personalizada ao pedido
            Dado que o modal de personalização da pizza "Napolitana" no tamanho "Grande" está aberto
            E a pizza "Napolitana" possui o ingrediente "Bacon" como adicional
            E a quantidade do "Bacon" está em "1"
            E a quantidade do "Pimentão" está em "2"
            Quando clico em adicionar a pizza personalizada ao pedido
            Então o resumo deve exibir a pizza "Napolitana" com o valor correspondente à personalização realizada

Funcionalidade: Definir atendimento e cliente
    A fim de registrar corretamente como o pedido será atendido e para quem
    Como um atendente
    Eu quero escolher o tipo de atendimento e o tipo de cliente
    De modo a vincular o pedido às informações corretas

    Contexto:
        Dado que estou na tela de registro de pedido
        E acesso a etapa "Atendimento"

        Cenário: Seleção de tipo de atendimento Entrega
            Quando seleciono o tipo de atendimento "Entrega"
            Então a opção de tipo de cliente "Consumidor Final" deve ficar desabilitada

        Cenário: Seleção de tipo de cliente Consumidor Final
            Quando seleciono o tipo de cliente "Consumidor Final"
            Então a seção de clientes deve desaparecer
            E deve aparecer a frase "Pedido sem cliente vinculado."

        Cenário: Seleção de tipo de cliente Cliente Identificado
            Dado que selecionei o tipo de cliente "Consumidor Final"
            Quando seleciono o tipo de cliente "Cliente"
            Então a seção de clientes deve aparecer

Funcionalidade: Aplicar taxas e descontos no pedido
    A fim de cobrar o valor correto do cliente
    Como um atendente
    Eu quero que taxas e descontos sejam aplicados automaticamente ao pedido
    De modo a evitar erros de cálculo no resumo

    Contexto:
        Dado que estou na tela de registro de pedido

        Cenário: Aplicação da taxa de entrega com produtos no pedido
            Dado que a pizza "Calabresa" foi adicionada ao pedido
            E acesso a etapa "Atendimento"
            E vinculo o cliente "Ana Beatriz Souza"
            Quando seleciono o tipo de atendimento "Entrega"
            Então o resumo deve exibir a taxa de entrega de "R$ 10,00"

        Cenário: Aplicação do desconto de fidelidade para cliente elegível
            Dado que o cliente "Artur Alves" possui um benefício de fidelidade
            E a pizza "Frango com Catupiry" foi adicionada ao pedido
            Quando acesso a etapa "Atendimento"
            E vinculo o cliente "Artur Alves"
            Então o resumo deve exibir o desconto de fidelidade

        Cenário: Não aplicação do desconto de fidelidade para cliente não elegível
            Dado que o cliente "Gabriel Santos Rocha" não possui um benefício de fidelidade
            E a pizza "Pepperoni" foi adicionada ao pedido
            Quando acesso a etapa "Atendimento"
            E vinculo o cliente "Gabriel Santos Rocha"
            Então o resumo não deve exibir o desconto de fidelidade

        Cenário: Aplicação do desconto de promoção para pizzas elegíveis
            Dado que existe uma promoção vigente para pizzas "Grandes" a partir de 2 unidades
            Quando adiciono 2 pizzas "Frango com Catupiry" no tamanho "Grande"
            Então o resumo deve exibir o desconto de promoção

        Cenário: Não aplicação do desconto de promoção para pizzas não elegíveis
            Dado que existe uma promoção vigente para pizzas "Grandes" a partir de 2 unidades
            Quando adiciono 1 pizza "Vegetariana" no tamanho "Grande"
            Então o resumo não deve exibir o desconto de promoção

Funcionalidade: Finalizar pedido
    A fim de enviar o pedido para a cozinha
    Como um atendente
    Eu quero registrar o pedido apenas quando ele estiver válido
    De modo a evitar pedidos vazios ou incorretos

    Contexto:
        Dado que estou na tela de registro de pedido

        Cenário: Tentativa de finalizar pedido sem itens
            Dado que acesso a etapa "Atendimento"
            Quando clico em "Registrar pedido"
            Então deve aparecer a mensagem de erro "Não é possível registrar um pedido sem itens."

        Cenário: Finalização de pedido com sucesso
            Dado que a pizza "Bacon" foi adicionada ao pedido
            E acesso a etapa "Atendimento"
            E vinculo o cliente "Artur Alves"
            Quando clico em "Registrar pedido"
            Então deve aparecer a mensagem de pedido realizado
            E devo ser redirecionado para a home
