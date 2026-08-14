@extends('layouts.app')

@section('title', 'Erro interno')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <main class="corpo__conteudo" style="max-width:640px;text-align:center;margin-top:80px;">
        <p style="font-size:4rem;margin:0;color:var(--cor-erro);font-weight:900;">500</p>
        <h1 style="margin:8px 0;">Algo deu errado</h1>
        <p style="color:var(--cor-texto-suave);">
            Ocorreu um erro inesperado no sistema. Tente novamente em instantes.
            Se o problema persistir, procure a equipe de TI.
        </p>
        <a class="botao" href="{{ route('totem.index') }}">Voltar ao início</a>
    </main>
</div>
@endsection
