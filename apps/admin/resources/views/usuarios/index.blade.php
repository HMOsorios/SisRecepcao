@extends('layouts.app')

@section('title', 'Usuários e Perfis')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Usuários e Perfis</h1>
            <p class="cabecalho__subtitulo">
                Realm do Keycloak — administrado pela TI (Seção 8.3)
            </p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('home.dashboard') }}">Início</a>
            <a class="botao botao--destaque" href="{{ route('auth.logout') }}">Sair</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <section class="cartao">
            <h2 class="cartao__titulo">Perfis de acesso</h2>
            <table class="tabela">
                <thead><tr><th>Perfil</th></tr></thead>
                <tbody>
                    @foreach ($perfis as $perfil)
                        <tr>
                            <td><strong>{{ $perfil }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="cartao" style="margin-top:1.5rem;">
            <h2 class="cartao__titulo">Usuários do realm</h2>
            <table class="tabela">
                <thead>
                    <tr><th>Nome</th><th>Usuário</th><th>E-mail</th><th>Ativo</th></tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario['firstName'] ?? '—' }} {{ $usuario['lastName'] ?? '' }}</td>
                            <td>{{ $usuario['username'] ?? '—' }}</td>
                            <td>{{ $usuario['email'] ?? '—' }}</td>
                            <td>{{ ($usuario['enabled'] ?? false) ? 'Sim' : 'Não' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Nenhum usuário retornado (o painel de usuários depende das credenciais do admin-cli).</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape">
        SisRecepção v{{ config('app.version') }} — {{ config('sms.nome') }}
    </footer>
</div>
@endsection
