@extends('layouts.app')

@section('title', 'Painel de Chamadas')
@section('body-class', 'painel')

@section('content')
<div class="painel">
    <header class="painel__topo">
        <div class="painel__orgao">{{ config('sms.nome') }}</div>
        <div class="painel__relogio" data-relogio></div>
    </header>

    <main class="painel__principal" data-painel>
        <p class="painel__chamada-senha" data-senha>---</p>
        <p class="painel__chamada-guiche" data-guiche></p>
        <p class="painel__chamada-cliente" data-cliente></p>
    </main>

    <section class="painel__historico" data-historico aria-label="Histórico de chamadas"></section>
</div>
@endsection

@push('head')
    <meta name="painel" data-dados-url="{{ $dadosUrl }}"
          data-mercure-url="{{ $mercureUrl }}"
          data-poll="{{ $pollInterval }}"
          data-reconnect="{{ $reconnectDelay }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/painel.js') }}"></script>
@endpush
