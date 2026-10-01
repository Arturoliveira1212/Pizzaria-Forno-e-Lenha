<?php

class RepositorioClienteEmBDR implements RepositorioCliente {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterClientes(FiltroCliente $filtro): array {
        $parametros = [];
        $sql = $this->gerarQuery($filtro, $parametros);
        $resultado = $this->bancoDados->consultar($sql, $parametros);

        foreach ($resultado as $linha) {
            $clientes[] = $this->transformarClienteEmObjeto($linha);
        }

        return $clientes ?? [];
    }

    /**
     * @param array<string, mixed> $linha
     */
    private function transformarClienteEmObjeto(array $linha): Cliente {
        $endereco = $linha['idEndereco'] !== null
            ? new Endereco(
                logradouro: $linha['logradouro'],
                numero: $linha['numero'],
                bairro: $linha['bairro'],
                cidade: $linha['cidade'],
                estado: $linha['estado'],
                uf: $linha['uf'],
            )
            : null;

        $beneficioFidelidade = $linha['idBeneficioFidelidade'] !== null
            ? new BeneficioFidelidade(
                id: (int) $linha['idBeneficioFidelidade'],
                percentualDesconto: new Percentual((int) $linha['percentualDesconto']),
                dataConcessao: $linha['dataConcessao'] !== null ? new DateTime($linha['dataConcessao']) : null,
                validade: $linha['validade'] !== null ? new DateTime($linha['validade']) : null,
                utilizado: (bool) $linha['utilizado'],
            )
            : null;

        return new Cliente(
            id: (string) $linha['id'],
            nome: $linha['nome'],
            telefone: Telefone::criar($linha['telefone']),
            cpf: new Cpf($linha['cpf']),
            endereco: $endereco,
            beneficioFidelidade: $beneficioFidelidade,
        );
    }

    /**
     * @param array<string, mixed> $parametros
     */
    private function gerarQuery(FiltroCliente $filtro, array &$parametros): string {
        $query = 'SELECT
                cliente.id,
                cliente.nome,
                cliente.telefone,
                cliente.cpf,
                endereco.id AS idEndereco,
                endereco.logradouro,
                endereco.numero,
                endereco.bairro,
                endereco.cidade,
                endereco.estado,
                endereco.uf,
                beneficio_fidelidade.id AS idBeneficioFidelidade,
                beneficio_fidelidade.percentualDesconto,
                beneficio_fidelidade.dataConcessao,
                beneficio_fidelidade.validade,
                beneficio_fidelidade.utilizado
            FROM cliente
                LEFT JOIN beneficio_fidelidade ON cliente.id = beneficio_fidelidade.idCliente
                LEFT JOIN endereco ON cliente.id = endereco.idCliente
                    WHERE 1=1';

        if ($filtro->identificador) {
            $query .= ' AND cliente.id = :id OR cliente.nome LIKE :nome OR cliente.cpf LIKE :cpf';

            $parametros['id'] = $filtro->identificador;
            $parametros['nome'] = '%' . $filtro->identificador . '%';
            $parametros['cpf'] = $filtro->identificador . '%';
        }

        return $query;
    }

    public function obterClientePorId(string $idCliente): ?Cliente {
        return null;
    }

    public function marcarBeneficioFidelidadeComoUtilizado(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void {
        $sql = 'UPDATE beneficio_fidelidade SET utilizado = :utilizado
            WHERE idCliente = :idCliente
            AND id = :id';

        $this->bancoDados->executar($sql, [
            'id' => $beneficioFidelidade->id,
            'idCliente' => $cliente->id,
            'utilizado' => true,
        ]);
    }

    public function registrarBeneficioFidelidade(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void {
        $sql = 'INSERT INTO beneficio_fidelidade (
                idCliente,
                percentualDesconto,
                dataConcessao,
                validade,
                utilizado
            ) VALUES (
                :idCliente,
                :percentualDesconto,
                :dataConcessao,
                :validade,
                :utilizado
            )';

        $this->bancoDados->executar($sql, [
            'idCliente' => $cliente->id,
            'percentualDesconto' => $beneficioFidelidade->percentualDesconto->valor,
            'dataConcessao' => $beneficioFidelidade->dataConcessao->format('Y-m-d H:i:s'),
            'validade' => $beneficioFidelidade->validade->format('Y-m-d H:i:s'),
            'utilizado' => $beneficioFidelidade->utilizado,
        ]);
    }
}
