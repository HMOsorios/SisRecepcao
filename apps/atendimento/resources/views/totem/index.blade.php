@extends('layouts.app')

@section('title', 'Totem de Atendimento')
@section('body-class', 'totem')

@section('content')
<div class="totem">
    <header class="totem__topo">
        <div>
            <h1 class="totem__titulo">{{ config('sms.nome') }}</h1>
            <div>Totem de autoatendimento</div>
        </div>
        <div class="totem__relogio" data-relogio></div>
        <button type="button" class="botao botao--contorno" data-audio style="border-color:#fff;color:#fff;">Áudio: ligado</button>
    </header>

    <main class="totem__conteudo">
        <div class="alerta alerta--erro" data-erro hidden></div>

        <form method="POST" action="{{ route('totem.emitir') }}" data-form-emissao>
            @csrf
            <input type="hidden" name="servico" data-servico-input>
            <input type="hidden" name="prioridade" data-prioridade-input>
            <input type="hidden" name="metadata[totem]" value="1">
            <input type="hidden" data-honeypot name="{{ config('honeypot.field') }}" value="" tabindex="-1" autocomplete="off">
            <input type="hidden" data-tempo name="{{ config('honeypot.tempo_field') }}" value="" tabindex="-1" autocomplete="off">

            {{-- Passo 1: serviço --}}
            <section data-passo="1" data-painel-servicos>
                <h2 class="totem__passo-titulo">1. Escolha o tipo de atendimento</h2>
                <div class="totem__grade-servicos">
                    @foreach ($servicos as $servico)
                        <button type="button" class="totem__servico"
                                onclick="escolherServico({{ $servico['id'] }}, this); irPara(2);">
                            <span class="totem__servico-icone" aria-hidden="true">
                                @switch($servico['icone'] ?? '')
                                    @case('documentos') 📄 @break
                                    @case('servidores') 🧑‍💼 @break
                                    @case('fornecedores') 💰 @break
                                    @case('ouvidoria') 🗣️ @break
                                    @case('gabinete') 🏛️ @break
                                    @case('transporte') 🚐 @break
                                    @case('avaliacoes') 📊 @break
                                    @default 🏥
                                @endswitch
                            </span>
                            {{ $servico['nome'] }}
                        </button>
                    @endforeach
                </div>
            </section>

            {{-- Passo 2: prioridade --}}
            <section data-passo="2" data-painel-prioridades hidden>
                <h2 class="totem__passo-titulo">2. Você se enquadra em alguma prioridade legal?</h2>
                <div class="totem__grade-prioridades">
                    @foreach ($prioridades as $prioridade)
                        <button type="button" class="totem__prioridade"
                                style="background: {{ $prioridade['cor'] }};"
                                onclick="escolherPrioridade({{ $prioridade['id'] }}, this); irPara(3);">
                            {{ $prioridade['nome'] }}
                        </button>
                    @endforeach
                </div>
            </section>

            {{-- Passo 3: dados opcionais + emitir --}}
            <section data-passo="3" data-painel-dados hidden>
                <h2 class="totem__passo-titulo">3. Confirme e retire sua senha</h2>
                <div class="cartao">
                    <div class="campo">
                        <label class="campo__rotulo" for="nome">Nome (opcional)</label>
                        <input class="campo__entrada" id="nome" data-nome name="nome" maxlength="120" autocomplete="off">
                        <p class="campo__dica">Opcional — identifica você no painel de chamada.</p>
                    </div>
                    <div class="campo">
                        <label class="campo__rotulo" for="documento">CPF (opcional)</label>
                        <input class="campo__entrada" id="documento" data-documento name="documento" maxlength="14" autocomplete="off">
                        <p class="campo__dica">Usado apenas para agilizar o atendimento.</p>
                    </div>
                    <div class="totem__acoes">
                        <button type="button" class="botao botao--contorno totem__back" data-voltar>← Voltar</button>
                        <button type="submit" class="botao botao--destaque totem__emitir" data-emitir>Emitir senha 🎫</button>
                    </div>
                </div>
            </section>
        </form>
    </main>
</div>
@endsection

@push('head')
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0b6e4f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
@endpush

@push('scripts')
    <script src="{{ asset('js/totem.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {});
        }
    </script>
@endpush
