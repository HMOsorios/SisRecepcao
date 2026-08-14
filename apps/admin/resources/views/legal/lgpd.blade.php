@extends('layouts.app')

@section('title', 'LGPD')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">LGPD — Lei Geral de Proteção de Dados</h1>
            <p class="cabecalho__subtitulo">Lei nº 13.709/2018 · atualizado em {{ $atualizadoEm->format('d/m/Y') }}</p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--destaque" href="{{ route('home.index') }}">Voltar ao início</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <article class="cartao">
            <h2>1. Base legal</h2>
            <p>
                O tratamento de dados realizado pelo SisRecepção encontra fundamento no
                interesse público e no exercício de competência legal da administração
                pública (Art. 7º, III, e Art. 23 da LGPD), voltado à prestação do serviço
                público de saúde e ao controle de atendimento.
            </p>

            <h2>2. Minimização</h2>
            <p>
                O sistema coleta somente os dados necessários ao atendimento. Números de
                CPF são apresentados mascarados nos crachás e telas, e eliminados
                automaticamente após o prazo legal (expurgo).
            </p>

            <h2>3. Encaminhamento (SI — Sensíveis)</h2>
            <p>
                Pacientes podem escolher ser encaminhados sem identificação, por meio de
                agendamento com senha, respeitando a privacidade de dados sensíveis de
                saúde.
            </p>

            <h2>4. Encargo do controlador</h2>
            <p>
                A {{ config('sms.nome') }} atua como controladora dos dados tratados.
                O cidadão pode exercer seus direitos (acesso, retificação, eliminação)
                pelos canais oficiais do órgão.
            </p>

            <h2>5. Segurança</h2>
            <p>
                Acesso autenticado via Keycloak, registro de auditoria, criptografia em
                trânsito e políticas de retenção automática compõem as medidas de
                segurança adotadas.
            </p>
        </article>
    </main>

    <footer class="rodape">SisRecepção v{{ config('app.version') }} — {{ config('sms.nome') }}</footer>
</div>
@endsection
