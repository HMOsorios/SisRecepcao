@extends('layouts.app')

@section('title', 'Console do Atendente')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Console do Atendente — {{ config('sms.nome') }}</h1>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('auth.logout') }}" style="border-color:#fff;color:#fff;">Sair ({{ $usuarioNome }})</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <meta name="console"
              data-fila-url="{{ $filaUrl }}"
              data-mercure-url="{{ $mercureUrl }}"
              data-unidade-id="{{ $unidadeId }}"
              data-departamento-id="{{ $departamentoId ?? '' }}"
              data-poll="{{ $pollInterval }}">

        <div class="console__grade">
            <section class="cartao">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                    <h2 class="cartao__titulo" style="margin:0;">Fila de atendimento</h2>
                    <button type="button" class="botao botao--destaque" data-proxima style="margin-left:auto;">Próxima senha</button>
                </div>

                <div class="alerta alerta--erro" data-erro hidden></div>
                <ul class="fila" data-fila>
                    <li style="color:var(--cor-texto-suave);">Carregando fila…</li>
                </ul>
            </section>

            <aside class="console__painel-lateral">
                <section class="cartao">
                    <h2 class="cartao__titulo">Guichê</h2>
                    <div class="campo">
                        <label class="campo__rotulo" for="local">Local / Mesa</label>
                        <select class="campo__entrada" id="local">
                            @foreach ($locais as $local)
                                <option value="{{ $local['id'] }}">{{ $local['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="campo">
                        <label class="campo__rotulo" for="numeroLocal">Número do guichê</label>
                        <input class="campo__entrada" id="numeroLocal" type="number" min="0" value="1">
                    </div>
                </section>

                <section class="cartao">
                    <h2 class="cartao__titulo">Status do atendente</h2>
                    <div class="status-chips">
                        <button type="button" class="botao botao--contorno" data-status="DISPONIVEL">Disponível</button>
                        <button type="button" class="botao botao--contorno" data-status="EM_ATENDIMENTO">Em atendimento</button>
                        <button type="button" class="botao botao--contorno" data-status="PAUSA">Pausa</button>
                        <button type="button" class="botao botao--contorno" data-status="AUSENTE">Ausente</button>
                    </div>
                </section>

                <section class="cartao">
                    <h2 class="cartao__titulo">Notificações do setor</h2>
                    <div data-notificacoes>
                        <p style="color:var(--cor-texto-suave);">Nenhuma notificação pendente.</p>
                    </div>
                </section>
            </aside>
        </div>
    </main>

    <footer class="rodape">
        {{ config('sms.nome') }} — SisRecepção v{{ config('app.version') }} — Console do setor
    </footer>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/atendente.js') }}"></script>
@endpush
