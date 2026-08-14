@extends('layouts.app')

@section('title', 'Entrar — Portal Administrativo')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <main class="corpo__conteudo" style="max-width:520px;margin-top:60px;">
        <div class="cartao" style="border-top:6px solid #2563eb;text-align:center;padding:2.5rem 2rem;">
            <div style="font-size:2.5rem;margin-bottom:.5rem;" aria-hidden="true">🏛️</div>
            <span style="display:inline-block;background:#dbeafe;color:#1d4ed8;font-weight:600;font-size:.78rem;letter-spacing:.04em;text-transform:uppercase;padding:.25rem .7rem;border-radius:999px;margin-bottom:1rem;">
                Portal Administrativo
            </span>
            <h1 style="margin:0 0 .5rem;">Entrar no Admin — SMS</h1>
            <p style="color:var(--cor-texto-suave);margin-bottom:1.75rem;">
                BI, Diretoria, Usuários e Configurações. Use sua conta de
                <strong>Developer, Admin, Diretoria ou Servidor</strong>.
                Não é aqui que o atendente da recepção faz login — o console
                do atendente fica no app <strong>Atendimento</strong>.
            </p>
            <a class="botao botao--destaque" href="{{ route('auth.login.iniciar') }}">Continuar para o login →</a>
        </div>
    </main>
</div>
@endsection
