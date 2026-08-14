<?php

namespace App\Services\Novosga;

/**
 * Sinaliza que o NovoSGA está inacessível (timeout/rede/circuit breaker).
 * O chamador deve recorrer ao modo de contingência (Seção 9.1).
 */
class NovosgaIndisponivelException extends NovosgaApiException {}
