@extends('layouts.app')

@section('title', 'Crachá Temporário')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho no-print">
        <h1 class="cabecalho__titulo">Crachá Temporário</h1>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('cracha.form') }}" style="border-color:#fff;color:#fff;">Novo visitante</a>
            <button type="button" class="botao botao--destaque" onclick="window.print()">Imprimir crachá</button>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <div class="grade" style="place-items:center;">
            <div class="cracha">
                <div class="cracha__cabecalho">
                    {{ config('sms.nome') }} — RECEPÇÃO
                </div>

                <div class="cracha__foto">
                    @if ($cracha->visitante->foto)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cracha->visitante->foto) }}"
                             alt="Foto do visitante" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                    @else
                        👤
                    @endif
                </div>

                <div class="cracha__nome">{{ $cracha->visitante->nome }}</div>

                <div class="cracha__linha">
                    <span class="cracha__rotulo">Documento</span>
                    <span class="cracha__valor">{{ $cpfMascarado }}</span>
                </div>
                <div class="cracha__linha">
                    <span class="cracha__rotulo">Setor</span>
                    <span class="cracha__valor">{{ $cracha->visitante->setor_destino ?? '—' }}</span>
                </div>
                <div class="cracha__linha">
                    <span class="cracha__rotulo">Sala</span>
                    <span class="cracha__valor">{{ $cracha->visitante->sala_destino ?? '—' }}</span>
                </div>
                <div class="cracha__linha">
                    <span class="cracha__rotulo">Emitido em</span>
                    <span class="cracha__valor">{{ $cracha->emitido_em->format('d/m/Y H:i') }}</span>
                </div>
                <div class="cracha__linha">
                    <span class="cracha__rotulo">Válido até</span>
                    <span class="cracha__valor">{{ $cracha->expira_em?->format('d/m/Y H:i') ?? '—' }}</span>
                </div>

                <div class="cracha__codigo">{{ $cracha->codigo }}</div>
            </div>
        </div>

        <div class="no-print" style="margin-top:24px;text-align:center;">
            <form method="POST" action="{{ route('cracha.devolver', $cracha) }}">
                @csrf
                <button type="submit" class="botao botao--perigo">Devolver / invalidar crachá</button>
            </form>
        </div>
    </main>

    <footer class="rodape no-print">
        {{ config('sms.nome') }} — SisRecepção v{{ config('app.version') }}
    </footer>
</div>
@endsection
