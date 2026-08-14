@extends('layouts.app')

@section('title', 'Acesso não autorizado')
@section('body-class', 'app-body')

@section('content')
<div class="corpo corpo--erro">
    <main class="corpo__conteudo erro">
        <div class="erro__codigo" aria-hidden="true">403</div>
        <h1 class="erro__titulo">Acesso não autorizado</h1>
        <p class="erro__descricao">
            {{ $exception->getMessage() ?: 'Seu perfil não tem permissão para acessar esta página.' }}
        </p>
        <a class="botao botao--destaque" href="{{ route('home.index') }}">Ir para o início</a>
        <a class="botao botao--contorno" href="{{ route('auth.logout') }}">Sair e entrar com outra conta</a>
    </main>
</div>
@endsection
