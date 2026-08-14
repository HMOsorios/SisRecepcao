@extends('layouts.app')

@section('title', 'Termos de Uso')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Termos de Uso</h1>
            <p class="cabecalho__subtitulo">Atualizado em {{ $atualizadoEm->format('d/m/Y') }}</p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--destaque" href="{{ route('home.index') }}">Voltar ao início</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <article class="cartao">
            <h2>1. Aceite dos termos</h2>
            <p>
                Ao utilizar o SisRecepção — sistema de gestão de recepção e atendimento
                da {{ config('sms.nome') }} — o usuário declara estar ciente e de acordo
                com estes termos, que regem o acesso ao sistema e o uso dos dados dele.
            </p>

            <h2>2. Finalidade</h2>
            <p>
                O sistema destina-se exclusivamente ao uso institucional da
                {{ config('sms.nome') }}, incluindo emissão de senhas, geração de senhas
                para atendimento, painel de acompanhamento, indicadores de BI e
                prestação de contas da diretoria.
            </p>

            <h2>3. Acesso institucional</h2>
            <p>
                O acesso é feito com a conta institucional (Keycloak) do órgão. O usuário
                é responsável pela guarda de suas credenciais e por toda atividade
                realizada com sua conta.
            </p>

            <h2>4. Uso dos dados</h2>
            <p>
                Os dados coletados (nome, CPF e telefone, quando informados) destinam-se
                ao controle de atendimento e ao funcionamento do sistema, nos termos da
                Política de Privacidade e da LGPD (Lei nº 13.709/2018).
            </p>

            <h2>5. Vedações</h2>
            <p>
                É vedado o uso do sistema para fins diversos dos institucionais, a
                tentativa de acesso não autorizado, o compartilhamento indevido de
                credenciais e a utilização de dados para finalidades não previstas.
            </p>

            <h2>6. Alterações</h2>
            <p>
                Estes termos podem ser atualizados a qualquer momento. A data de
                atualização consta no início desta página.
            </p>
        </article>
    </main>

    <footer class="rodape">SisRecepção v{{ config('app.version') }} — {{ config('sms.nome') }}</footer>
</div>
@endsection
