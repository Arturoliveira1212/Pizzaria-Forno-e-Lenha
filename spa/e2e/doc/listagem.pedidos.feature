Funcionalidade: Registrar saída do pedido para entrega
    A fim de saber quem levou cada pedido e quando ele saiu
    Como um atendente
    Eu quero informar o entregador ao alterar o status do pedido para "SAIU_PARA_ENTREGA"
    De modo a acompanhar a entrega do pedido até o cliente

    Contexto:
        Dado que existe um pedido de entrega com status "EM_PREPARACAO"
        E estou na tela de listagem de pedidos

        Cenário: Saída para entrega com entregador disponível
            Quando altero o status do pedido para "SAIU_PARA_ENTREGA"
            Então devem aparecer para seleção apenas os entregadores disponíveis "João Pedro Alves" e "Marcos Vinícius Rocha"
            Quando seleciono o entregador "João Pedro Alves" e registro a saída
            Então o pedido deve aparecer com o status "SAIU_PARA_ENTREGA"

Funcionalidade: Cancelar pedido
    A fim de manter o histórico dos pedidos que não foram concluídos
    Como um atendente
    Eu quero cancelar o pedido informando o motivo
    De modo a registrar por que o pedido não foi concluído

    Contexto:
        Dado que existe um pedido de balcão com status "RECEBIDO"
        E estou na tela de listagem de pedidos

        Cenário: Cancelamento de pedido com motivo
            Quando altero o status do pedido para "CANCELADO"
            E informo o motivo "Cliente desistiu do pedido" e confirmo o cancelamento
            Então o pedido deve aparecer com o status "CANCELADO"

        Cenário: Tentativa de cancelamento de pedido sem motivo
            Quando altero o status do pedido para "CANCELADO"
            E confirmo o cancelamento sem informar o motivo
            Então o modal de cancelamento deve continuar aberto exigindo o motivo
