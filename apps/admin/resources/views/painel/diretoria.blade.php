@extends('layouts.app')

@section('title', 'Painel da Diretoria')
@section('body-class', 'app-body')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Painel da Diretoria Administrativa</h1>
            <p class="cabecalho__subtitulo">
                {{ config('sms.nome') }} · atualizado em
                {{ $atualizadoEm->format('d/m/Y H:i') }}
                · <a href="{{ route('bi.resumo') }}" style="color:inherit;">BI detalhado</a>
            </p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('diretoria.relatorio') }}">Relatório PDF</a>
            <a class="botao botao--destaque" href="{{ route('auth.logout') }}">Sair</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <div class="grade-cartoes">
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Atendimentos hoje</span>
                <span class="cartao__valor">{{ $resumo['total_dia'] }}</span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Em fila agora</span>
                <span class="cartao__valor">{{ $resumo['em_fila'] }}</span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Tempo médio de espera</span>
                <span class="cartao__valor">
                    {{ $resumo['tempo_medio_espera_s'] !== null ? gmdate('i\m', $resumo['tempo_medio_espera_s']) : '—' }}
                </span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Tempo médio de atendimento</span>
                <span class="cartao__valor">
                    {{ $resumo['tempo_medio_atendimento_s'] !== null ? gmdate('i\m', $resumo['tempo_medio_atendimento_s']) : '—' }}
                </span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Não compareceu</span>
                <span class="cartao__valor">{{ $resumo['nao_compareceu'] }}</span>
            </div>
        </div>

        <div class="grade-2">
            <section class="cartao">
                <h2 class="cartao__titulo">Atendimentos por setor</h2>
                <table class="tabela">
                    <thead>
                        <tr><th>Setor/Serviço</th><th class="num">Atendimentos</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($porSetor as $item)
                            <tr>
                                <td>{{ $item['setor'] }}</td>
                                <td class="num">{{ $item['total'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2">Sem dados disponíveis.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="cartao">
                <h2 class="cartao__titulo">Situação das senhas</h2>
                <table class="tabela">
                    <thead>
                        <tr><th>Status</th><th class="num">Quantidade</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($porStatus as $item)
                            <tr>
                                <td>{{ $item['rotulo'] }}</td>
                                <td class="num">{{ $item['total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <footer class="rodape">
        SisRecepção v{{ config('app.version') }} — {{ config('sms.nome') }}
    </footer>
</div>
@endsection
