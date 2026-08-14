@extends('layouts.app')

@section('title', 'Política de Privacidade')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Política de Privacidade</h1>
            <p class="cabecalho__subtitulo">Atualizado em {{ $atualizadoEm->format('d/m/Y') }}</p>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--destaque" href="{{ route('home.index') }}">Voltar ao início</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        <article class="cartao">
            <h2>1. Dados coletados</h2>
            <p>
                Para o funcionamento do atendimento, o sistema pode coletar: nome,
                CPF (documento), telefone e data de nascimento do cidadão, além de
                registros de senha emitida, chamada e atendimento. No painel
                administrativo, registram-se usuário, data e hora das ações.
            </p>

            <h2>2. Finalidade</h2>
            <p>
                Os dados destinam-se ao controle de filas, prestação do atendimento,
                geração de indicadores gerenciais (BI) e auditoria do sistema.
            </p>

            <h2>3. Guarda e segurança</h2>
            <p>
                Os dados são armazenados em ambiente controlado com controle de acesso,
                criptografia em trânsito e registro de auditoria das ações administrativas.
            </p>

            <h2>4. Retenção e eliminação</h2>
            <p>
                Dados de visitantes são eliminados automaticamente após o prazo legal
                (expurgo automático). Dados agregados são mantidos apenas para fins
                estatísticos, sem identificação pessoal.
            </p>

            <h2>5. Direitos do titular</h2>
            <p>
                O titular pode solicitar acesso, correção e eliminação de seus dados,
                conforme a LGPD, pelos canais oficiais da {{ config('sms.nome') }}.
            </p>

            <h2>6. Compartilhamento</h2>
            <p>
                Não há venda ou cessão de dados pessoais a terceiros. O compartilhamento
                ocorre apenas com o NovoSGA, na condição de sistema provedor do controle
                de filas.
            </p>
        </article>
    </main>

    <footer class="rodape">SisRecepção v{{ config('app.version') }} — {{ config('sms.nome') }}</footer>
</div>
@endsection
