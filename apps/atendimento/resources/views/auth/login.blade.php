@extends('layouts.app')

@section('title', 'Entrar — Console do Atendente')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <main class="corpo__conteudo" style="max-width:520px;margin-top:60px;">
        <div class="cartao" style="border-top:6px solid var(--cor-destaque);text-align:center;padding:2.5rem 2rem;">
            @if ($errors->any())
                <div class="alerta alerta--erro" role="alert">{{ $errors->first('auth') }}</div>
            @endif

            <div style="font-size:2.5rem;margin-bottom:.5rem;" aria-hidden="true">🎫</div>
            <span style="display:inline-block;background:#fef3c7;color:#92400e;font-weight:600;font-size:.78rem;letter-spacing:.04em;text-transform:uppercase;padding:.25rem .7rem;border-radius:999px;margin-bottom:1rem;">
                Console do Atendente
            </span>
            <h1 style="margin:0 0 .5rem;">Entrar no Atendimento — SMS</h1>
            <p style="color:var(--cor-texto-suave);margin-bottom:1.75rem;">
                Fila, chamada de senha e notificações do setor. Use sua conta
                de <strong>Atendente ou Servidor</strong>.
                Não é aqui que Diretoria/BI/Configurações são acessados —
                isso fica no app <strong>Admin</strong>.
            </p>
            <a class="botao botao--destaque" href="{{ route('auth.login.iniciar') }}">Continuar para o login →</a>
        </div>
    </main>
</div>
@endsection
