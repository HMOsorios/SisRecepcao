@extends('layouts.app')

@section('title', 'Erro interno')
@section('body-class', 'app-body')

@section('content')
<div class="corpo corpo--erro">
    <main class="corpo__conteudo erro">
        <div class="erro__codigo" aria-hidden="true">500</div>
        <h1 class="erro__titulo">Erro interno do sistema</h1>
        <p class="erro__descricao">
            Ocorreu um erro inesperado. A equipe de TI já foi notificada.
            Tente novamente em alguns instantes.
        </p>
        <a class="botao botao--destaque" href="{{ route('home.index') }}">Ir para o início</a>
    </main>
</div>
@endsection
