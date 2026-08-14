@extends('layouts.app')

@section('title', 'Página não encontrada')
@section('body-class', 'app-body')

@section('content')
<div class="corpo corpo--erro">
    <main class="corpo__conteudo erro">
        <div class="erro__codigo" aria-hidden="true">404</div>
        <h1 class="erro__titulo">Página não encontrada</h1>
        <p class="erro__descricao">
            O endereço acessado não existe ou foi movido.
            Verifique o link e tente novamente.
        </p>
        <a class="botao botao--destaque" href="{{ route('home.index') }}">Ir para o início</a>
    </main>
</div>
@endsection
