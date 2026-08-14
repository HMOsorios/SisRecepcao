@extends('layouts.app')

@section('title', 'Portal da Secretaria')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho cabecalho--portal">
        <div>
            <h1 class="cabecalho__titulo">{{ config('sms.nome') }}</h1>
            <p class="cabecalho__subtitulo">{{ config('sms.identidade') }} — Portal institucional</p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('legal.termos') }}" style="border-color:#fff;color:#fff;">Termos</a>
            <a class="botao botao--contorno" href="{{ route('legal.privacidade') }}" style="border-color:#fff;color:#fff;">Privacidade</a>
            <a class="botao botao--destaque" href="{{ route('auth.login') }}">Entrar</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        @if (session('erro'))
            <div class="alerta alerta--erro" role="alert">{{ session('erro') }}</div>
        @endif

        <section class="portal">
            <div class="portal__hero">
                <h2>Bem-vindo ao SisRecepção</h2>
                <p>
                    Sistema de gestão de recepção e atendimento da {{ config('sms.nome') }}.
                    Acesse o painel administrativo, os indicadores de BI e o relatório
                    da diretoria com sua conta institucional.
                </p>
                <a class="botao botao--destaque" href="{{ route('auth.login') }}">Acessar o sistema</a>
            </div>

            <h2 class="portal__titulo">Acesso a outros sistemas da SMS</h2>
            <div class="portal__grade">
                @foreach (config('sms.sistemas') as $sistema)
                    <a class="cartao cartao--link"
                       href="{{ $sistema['url'] }}"
                       target="_blank" rel="noopener">
                        <div class="cartao__titulo">{{ $sistema['nome'] }}</div>
                        <p style="color:var(--cor-texto-suave);">Acesso ao sistema externo</p>
                    </a>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="rodape">
        {{ config('sms.nome') }} — SisRecepção v{{ config('app.version') }} ·
        <a href="{{ route('legal.lgpd') }}" style="color:inherit;">LGPD</a>
    </footer>
</div>
@endsection
