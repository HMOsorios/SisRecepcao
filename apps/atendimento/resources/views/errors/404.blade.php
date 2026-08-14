@extends('layouts.app')

@section('title', 'Página não encontrada')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <main class="corpo__conteudo" style="max-width:640px;text-align:center;margin-top:80px;">
        <p style="font-size:4rem;margin:0;color:var(--cor-primaria);font-weight:900;">404</p>
        <h1 style="margin:8px 0;">Página não encontrada</h1>
        <p style="color:var(--cor-texto-suave);">
            O endereço que você acessou não existe ou foi movido.
        </p>
        <a class="botao" href="{{ route('totem.index') }}">Voltar ao início</a>
    </main>
</div>
@endsection
