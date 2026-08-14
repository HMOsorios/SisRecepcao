@extends('layouts.app')

@section('title', 'BI — Indicadores de Atendimento')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">BI — Indicadores de Atendimento</h1>
            <p class="cabecalho__subtitulo">
                {{ config('sms.nome') }} · buscas em tempo real (Seção 2.5)
            </p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('diretoria.index') }}">Diretoria</a>
            <a class="botao botao--destaque" href="{{ route('auth.logout') }}">Sair</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <div class="grade-cartoes" id="kpis" aria-live="polite">
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Atendimentos hoje</span>
                <span class="cartao__valor">—</span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Em fila agora</span>
                <span class="cartao__valor">—</span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Espera média</span>
                <span class="cartao__valor">—</span>
            </div>
            <div class="cartao cartao--indicador">
                <span class="cartao__rotulo">Atendimento médio</span>
                <span class="cartao__valor">—</span>
            </div>
        </div>

        <section class="cartao" style="margin-top:1.5rem;">
            <h2 class="cartao__titulo">Busca em atendimentos</h2>
            <form id="formBusca" style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <input class="campo" type="search" name="q" placeholder="nome, documento, senha, setor..."
                       aria-label="Buscar atendimento" autocomplete="off" style="flex:1;min-width:240px;">
                <button class="botao botao--destaque" type="submit">Buscar</button>
            </form>
            <table class="tabela" style="margin-top:1rem;">
                <thead>
                    <tr><th>#</th><th>Senha</th><th>Setor</th><th>Status</th></tr>
                </thead>
                <tbody id="resultados">
                    <tr><td colspan="4">Digite um termo para buscar.</td></tr>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape">
        SisRecepção v{{ config('app.version') }} — {{ config('sms.nome') }}
    </footer>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const $ = (sel) => document.querySelector(sel);

    async function carregarKpis() {
        try {
            const resposta = await fetch('/bi/api/resumo');
            const dados = await resposta.json();
            const kpis = $('#kpis');
            kpis.children[0].children[1].textContent = dados.total_dia;
            kpis.children[1].children[1].textContent = dados.em_fila;
            kpis.children[2].children[1].textContent = dados.tempo_medio_espera_s !== null ? Math.round(dados.tempo_medio_espera_s / 60) + ' min' : '—';
            kpis.children[3].children[1].textContent = dados.tempo_medio_atendimento_s !== null ? Math.round(dados.tempo_medio_atendimento_s / 60) + ' min' : '—';
        } catch (erro) {
            kpis.children[1].children[1].textContent = 'offline';
        }
    }

    async function buscar(evento) {
        evento.preventDefault();
        const termo = $('#formBusca [name=q]').value.trim();
        const corpo = $('#resultados');

        if (termo === '') {
            corpo.innerHTML = '<tr><td colspan="4">Digite um termo para buscar.</td></tr>';
            return;
        }

        try {
            const resposta = await fetch('/bi/api/buscar?q=' + encodeURIComponent(termo));
            const dados = await resposta.json();
            const linhas = dados.resultados.map((a) => {
                const senha = a.senha ? (a.senha.sigla + ' ' + String(a.senha.numero).padStart(2, '0')) : '—';
                return '<tr><td>' + a.id + '</td><td>' + senha + '</td><td>' + (a.servico ? a.servico.nome : '—') + '</td><td>' + (a.status || '—').replace(/_/g, ' ') + '</td></tr>';
            });
            corpo.innerHTML = linhas.length
                ? linhas.join('')
                : '<tr><td colspan="4">Nenhum resultado para "' + termo + '".</td></tr>';
        } catch (erro) {
            corpo.innerHTML = '<tr><td colspan="4">Falha na busca.</td></tr>';
        }
    }

    $('#formBusca').addEventListener('submit', buscar);
    carregarKpis();
    setInterval(carregarKpis, 60000);
});
</script>
@endpush
