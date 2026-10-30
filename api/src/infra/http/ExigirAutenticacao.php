<?php

use phputil\router\HttpRequest;
use phputil\router\HttpResponse;

class ExigirAutenticacao {
    private const ROTAS_PUBLICAS = ['POST /login'];

    public function __construct(
        private readonly GestorAutenticacao $gestorAutenticacao,
    ) {}

    public function __invoke(HttpRequest &$request, HttpResponse &$response, bool &$stop): void {
        $rota = $request->method() . ' ' . $request->urlWithoutQueries();
        if (in_array($rota, self::ROTAS_PUBLICAS, true)) {
            return;
        }

        try {
            $this->gestorAutenticacao->validarSessao(
                ip: $_SERVER['REMOTE_ADDR'] ?? '',
                navegador: $request->header('User-Agent') ?? '',
            );
        } catch (AutenticacaoException $e) {
            $response->status(401)->json(['message' => 'Não autenticado.', 'data' => [
                'erros' => [$e->getMessage()],
            ]]);
            $stop = true;
        } catch (Throwable $e) {
            $response->status(500)->json(['message' => 'Houve um erro interno no servidor.']);
            $stop = true;
        }
    }
}
