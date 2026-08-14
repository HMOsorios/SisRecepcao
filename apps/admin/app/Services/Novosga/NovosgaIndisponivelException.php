<?php

namespace App\Services\Novosga;

/**
 * NovoSGA indisponível (rede fora/HTTP 5xx) — dispara modo de contingência.
 *
 * Estende NovosgaApiException (não RuntimeException direto) para que um
 * catch (NovosgaApiException) genérico também capture este caso — hierarquia
 * alinhada com o app atendimento, que é onde a distinção realmente importa
 * (Seção 9.1).
 */
class NovosgaIndisponivelException extends NovosgaApiException {}
