<?php

class RepositorioClienteEmBDR implements RepositorioCliente {
    public function __construct(
        private readonly BancoDados $bancoDados,
    ) {}

    public function obterClientes(FiltroCliente $filtro): array {
        $parametros = [];
        $sql = $this->gerarQuery($filtro, $parametros);

        $resultado = $this->bancoDados->consultar($sql, $parametros);
        if (empty($resultado)) {
            return [];
        }

        $idsClientes = array_column($resultado, 'id');
        $enderecos = $this->buscarEndereco($idsClientes);
        $beneficiosFidelidade = $this->buscarBeneficioFidelidade($idsClientes);

        $clientes = [];

        foreach ($resultado as $linha) {
            $idCliente = $linha['id'];

            $endereco = $enderecos[$idCliente][0] ?? null;
            $beneficioFidelidade = $beneficiosFidelidade[$idCliente][0] ?? null;

            $clientes[] = new Cliente(
                id: (string) $linha['id'],
                nome: $linha['nome'],
                telefone: Telefone::criar($linha['telefone']),
                cpf: new Cpf($linha['cpf']),
                endereco: $endereco,
                beneficioFidelidade: $beneficioFidelidade,
            );
        }

        return $clientes;
    }

    public function obterClientePorId(string $idCliente): ?Cliente {
        $sql = 'SELECT
            cliente.id,
            cliente.nome,
            cliente.telefone,
            cliente.cpf
                FROM cliente
                    WHERE cliente.id = :id';

        $resultado = $this->bancoDados->consultar($sql, [
            'id' => $idCliente,
        ]);
        if (empty($resultado)) {
            return null;
        }

        $linha = $resultado[0];

        $idsClientes = [$linha['id']];
        $enderecos = $this->buscarEndereco($idsClientes);
        $beneficiosFidelidade = $this->buscarBeneficioFidelidade($idsClientes);

        return new Cliente(
            id: (string) $linha['id'],
            nome: $linha['nome'],
            telefone: Telefone::criar($linha['telefone']),
            cpf: new Cpf($linha['cpf']),
            endereco: $enderecos[$linha['id']][0] ?? null,
            beneficioFidelidade: $beneficiosFidelidade[$linha['id']][0] ?? null,
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
                FROM cliente
                    WHERE 1=1';

        if ($filtro->identificador !== '') {
            $query .= ' AND (cliente.nome LIKE :nome OR cliente.cpf LIKE :cpf)';

            $parametros['nome'] = "%{$filtro->identificador}%";
            $parametros['cpf'] = "{$filtro->identificador}%";
        }

        if ($filtro->paginacao !== null) {
            $limite = $filtro->paginacao->limite;
            $offset = ($filtro->paginacao->pagina - 1) * $limite;

            $query .= " LIMIT {$limite} OFFSET {$offset}";
        }

        return $query;
    }

    /**
     * @param int[] $idsClientes
     * @return array<string, Endereco[]>
     */
    private function buscarEndereco(array $idsClientes): array {
        $idsClientesString = implode(',', $idsClientes);
        $sql = "SELECT
            endereco.id AS idEndereco,
            endereco.idCliente,
            endereco.logradouro,
            endereco.numero,
            endereco.bairro,
            endereco.cidade,
            endereco.estado,
            endereco.uf
                FROM endereco
                    WHERE idCliente IN ({$idsClientesString});";

        $resultado = $this->bancoDados->consultar($sql);

        return $this->agruparPorCliente($resultado, function (array $linha): Endereco {
            return new Endereco(
                id: (int) $linha['idEndereco'],
                logradouro: $linha['logradouro'],
                numero: $linha['numero'],
                bairro: $linha['bairro'],
                cidade: $linha['cidade'],
                estado: $linha['estado'],
                uf: $linha['uf'],
            );
        });
    }

    /**
     * @param int[] $idsClientes
     * @return array<string, BeneficioFidelidade[]>
     */
    private function buscarBeneficioFidelidade(array $idsClientes): array {
        $idsClientesString = implode(',', $idsClientes);
        $sql = "SELECT
            beneficio_fidelidade.id AS idBeneficioFidelidade,
            beneficio_fidelidade.idCliente,
            beneficio_fidelidade.percentualDesconto,
            beneficio_fidelidade.dataConcessao,
            beneficio_fidelidade.validade,
            beneficio_fidelidade.utilizado
                FROM beneficio_fidelidade
                    WHERE idCliente IN ({$idsClientesString})
                    AND beneficio_fidelidade.utilizado = :utilizado
                    AND beneficio_fidelidade.validade >= CURDATE()";

        $resultado = $this->bancoDados->consultar($sql, [
            'utilizado' => 0,
        ]);

        return $this->agruparPorCliente($resultado, function (array $linha): BeneficioFidelidade {
            return new BeneficioFidelidade(
                id: (int) $linha['idBeneficioFidelidade'],
                percentualDesconto: new Percentual((int) $linha['percentualDesconto']),
                dataConcessao: new DateTime($linha['dataConcessao']),
                validade: new DateTime($linha['validade']),
                utilizado: (bool) $linha['utilizado'],
            );
        });
    }

    /**
     * @param array<array<string, mixed>> $linhas
     * @return array<string, BeneficioFidelidade[]|Endereco[]>
     */
    private function agruparPorCliente(array $linhas, callable $criarObjeto): array {
        $agrupado = [];

        foreach ($linhas as $linha) {
            $idCliente = $linha['idCliente'];
            $agrupado[$idCliente][] = $criarObjeto($linha);
        }

        return $agrupado;
    }

    public function marcarBeneficioFidelidadeComoUtilizado(Cliente $cliente, BeneficioFidelidade $beneficioFidelidade): void {
        $sql = 'UPDATE beneficio_fidelidade SET utilizado = :utilizado
            WHERE idCliente = :idCliente
            AND id = :id';

        $this->bancoDados->executar($sql, [
            'id' => $beneficioFidelidade->id,
            'idCliente' => $cliente->id,
            'utilizado' => 1,
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
            'utilizado' => (int) $beneficioFidelidade->utilizado,
        ]);
    }
}
