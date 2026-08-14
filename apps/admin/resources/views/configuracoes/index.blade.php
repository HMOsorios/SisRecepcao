@extends('layouts.app')

@section('title', 'Configurações e Manutenção')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Configurações e Manutenção</h1>
            <p class="cabecalho__subtitulo">
                Exclusivo do perfil Developer (Seção 8.4) · v{{ $versao }}
            </p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('home.dashboard') }}">Início</a>
            <a class="botao botao--destaque" href="{{ route('auth.logout') }}">Sair</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        @if (session('ok'))
            <div class="alerta alerta--ok" role="status">{{ session('ok') }}</div>
        @endif
        @if (session('erro'))
            <div class="alerta alerta--erro" role="alert">{{ session('erro') }}</div>
        @endif

        <section class="cartao">
            <h2 class="cartao__titulo">Dados institucionais</h2>
            <form method="post" action="{{ route('configuracoes.salvar') }}">
                @csrf
                <div class="form-grid">
                    @foreach ($campos as $chave => $meta)
                        <div class="form-grid__item">
                            <label for="{{ $chave }}">{{ $meta['rotulo'] }}</label>
                            <input class="campo" id="{{ $chave }}" name="campos[{{ $chave }}]"
                                   type="text" value="{{ $valores[$chave] ?? '' }}" maxlength="500">
                            @error('campos.'.$chave)
                                <span class="erro">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
                <button class="botao botao--destaque" type="submit">Salvar configurações</button>
            </form>
        </section>

        <section class="cartao" style="margin-top:1.5rem;">
            <h2 class="cartao__titulo">Manutenção</h2>
            <p style="color:var(--cor-texto-suave);">
                Ações executadas no servidor. Repositório: {{ $githubRepo }} · raiz: {{ $baseDir }}
            </p>
            <form method="post" action="{{ route('configuracoes.acao', ['acao' => 'git_pull']) }}">
                @csrf
                <button class="botao botao--contorno" type="submit">Git pull</button>
            </form>
            <form method="post" action="{{ route('configuracoes.acao', ['acao' => 'migrar']) }}" style="margin-top:.5rem;">
                @csrf
                <button class="botao botao--contorno" type="submit">Rodar migrações</button>
            </form>
            <form method="post" action="{{ route('configuracoes.acao', ['acao' => 'recarregar_caches']) }}" style="margin-top:.5rem;">
                @csrf
                <button class="botao botao--contorno" type="submit">Recarregar caches</button>
            </form>
            <form method="post" action="{{ route('configuracoes.acao', ['acao' => 'deploy_coolify']) }}" style="margin-top:.5rem;">
                @csrf
                <button class="botao botao--contorno" type="submit">Deploy no Coolify</button>
            </form>
        </section>

        <section class="cartao" style="margin-top:1.5rem;">
            <h2 class="cartao__titulo">Últimas ações de auditoria</h2>
            <table class="tabela">
                <thead>
                    <tr><th>Quando</th><th>Usuário</th><th>Ação</th><th>Detalhe</th></tr>
                </thead>
                <tbody>
                    @forelse ($auditorias as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->usuario_nome }}</td>
                            <td>{{ $log->acao }}</td>
                            <td style="max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ is_array($log->detalhes) ? json_encode($log->detalhes) : $log->detalhes }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Nenhuma ação registrada ainda.</td></tr>
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
