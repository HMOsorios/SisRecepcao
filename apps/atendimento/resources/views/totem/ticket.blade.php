@extends('layouts.app')

@section('title', 'Sua Senha')
@section('body-class', 'totem')

@section('content')
<div class="totem">
    <header class="totem__topo">
        <div>
            <h1 class="totem__titulo">{{ config('sms.nome') }}</h1>
            <div>Sua senha foi emitida</div>
        </div>
    </header>

    <main class="totem__conteudo">
        <div class="totem__ticket">
            @if ($provisional)
                <div class="alerta alerta--aviso" role="status">
                    Senha provisória — o sistema está em modo de contingência. O atendimento será confirmado quando a conexão for restabelecida.
                </div>
            @endif

            <p class="totem__ticket-info">Guarde sua senha. Quando o painel chamar:</p>
            <p class="totem__ticket-senha" aria-live="polite">{{ $senha }}</p>
            <p class="totem__ticket-info">
                @if (is_array($ticket['servico'] ?? null))
                    {{ $ticket['servico']['nome'] ?? '' }}
                @elseif (! empty($ticket['servico']))
                    {{ $ticket['servico'] }}
                @endif
            </p>
            <p class="totem__ticket-info">{{ now()->format('d/m/Y H:i') }}</p>

            <div class="totem__ticket-qr">
                <img src="{{ $qrDataUri }}" alt="QR Code para acompanhamento da senha pelo celular"
                     width="220" height="220">
            </div>
            <p class="totem__ticket-info">Aponte a câmera do celular para acompanhar sua posição na fila.</p>

            <div class="totem__acoes">
                <button type="button" class="botao botao--contorno totem__back" onclick="window.print()">Imprimir senha</button>
                <a class="botao botao--destaque totem__emitir" href="{{ route('totem.index') }}">Nova senha</a>
            </div>
        </div>
    </main>
</div>
@endsection
