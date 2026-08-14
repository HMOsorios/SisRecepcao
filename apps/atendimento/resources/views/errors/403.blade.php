@extends('layouts.app')

@section('title', 'Acesso não autorizado')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <main class="corpo__conteudo" style="max-width:640px;text-align:center;margin-top:80px;">
        <p style="font-size:4rem;margin:0;color:var(--cor-primaria);font-weight:900;">403</p>
        <h1 style="margin:8px 0;">Acesso não autorizado</h1>
        <p style="color:var(--cor-texto-suave);">
            {{ $exception->getMessage() ?: 'Seu perfil não tem permissão para acessar esta página.' }}
        </p>
        <a class="botao" href="{{ route('totem.index') }}">Voltar ao início</a>
        <a class="botao botao--contorno" href="{{ route('auth.logout') }}">Sair e entrar com outra conta</a>
    </main>
</div>
@endsection
