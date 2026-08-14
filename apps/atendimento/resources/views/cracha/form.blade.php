@extends('layouts.app')

@section('title', 'Cadastro de Visitante')
@section('body-class', 'app-body')

@section('content')
<div class="corpo">
    <header class="cabecalho">
        <div>
            <h1 class="cabecalho__titulo">Recepção — Cadastro de Visitante e Crachá</h1>
        </div>
        <nav class="cabecalho__nav">
            <a class="botao botao--contorno" href="{{ route('totem.index') }}" style="border-color:#fff;color:#fff;">Totem</a>
            <a class="botao botao--contorno" href="{{ route('painel.index') }}" style="border-color:#fff;color:#fff;">Painel</a>
            <a class="botao botao--contorno" href="{{ route('atendente.index') }}" style="border-color:#fff;color:#fff;">Console</a>
        </nav>
    </header>

    <main class="corpo__conteudo">
        @if (session('ok'))
            <div class="alerta alerta--sucesso" role="status">{{ session('ok') }}</div>
        @endif

        @if ($errors->any())
            <div class="alerta alerta--erro" role="alert">
                <strong>Corrija os erros abaixo:</strong>
                <ul style="margin:8px 0 0;">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('cracha.registrar') }}" enctype="multipart/form-data" class="cartao">
            @csrf

            <div class="grade grade--2">
                <div class="campo">
                    <label class="campo__rotulo" for="nome">Nome completo *</label>
                    <input class="campo__entrada" id="nome" name="nome" value="{{ old('nome') }}" required maxlength="120">
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="documento">Documento (CPF/RG) *</label>
                    <input class="campo__entrada" id="documento" name="documento" value="{{ old('documento') }}" required maxlength="30">
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="tipo_documento">Tipo de documento</label>
                    <select class="campo__entrada" id="tipo_documento" name="tipo_documento">
                        <option value="CPF">CPF</option>
                        <option value="RG">RG</option>
                    </select>
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="setor_destino">Setor de destino *</label>
                    <select class="campo__entrada" id="setor_destino" name="setor_destino" required>
                        <option value="">Selecione…</option>
                        @foreach ($setores as $setor)
                            <option value="{{ $setor['nome'] }}" @selected(old('setor_destino') === $setor['nome'])>{{ $setor['nome'] }}</option>
                        @endforeach
                        @foreach (['Gabinete', 'Ouvidoria', 'Auditoria', 'Diretoria Administrativa', 'Diretoria Financeira', 'Recursos Humanos (RH)', 'Planejamento', 'Transporte', 'Controle e Avaliações', 'Diretoria de Atenção Básica (DAB)', 'Diretoria de Média e Alta Complexidade (MAC)', 'Saúde da Mulher', 'Doenças Crônicas', 'Extra Muro'] as $setor)
                            <option value="{{ $setor }}" @selected(old('setor_destino') === $setor)>{{ $setor }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="sala_destino">Sala autorizada</label>
                    <input class="campo__entrada" id="sala_destino" name="sala_destino" value="{{ old('sala_destino') }}" maxlength="120" placeholder="Ex.: Sala 204">
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="telefone">Telefone</label>
                    <input class="campo__entrada" id="telefone" name="telefone" value="{{ old('telefone') }}" maxlength="25" placeholder="(00) 00000-0000">
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="email">E-mail</label>
                    <input class="campo__entrada" type="email" id="email" name="email" value="{{ old('email') }}" maxlength="120">
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="foto">Foto (webcam/arquivo)</label>
                    <input class="campo__entrada" type="file" id="foto" name="foto" accept="image/*" capture="user">
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="imagem_documento">Imagem do documento (OCR — opcional)</label>
                    <input class="campo__entrada" type="file" id="imagem_documento" name="imagem_documento" accept="image/*">
                    <p class="campo__dica">Leitura automática (CPF/RG). Se o OCR falhar, digite manualmente.</p>
                </div>

                <div class="campo">
                    <label class="campo__rotulo" for="observacao">Observações</label>
                    <textarea class="campo__entrada" id="observacao" name="observacao" rows="2" maxlength="500">{{ old('observacao') }}</textarea>
                </div>
            </div>

            <button type="submit" class="botao botao--bloco">Cadastrar e emitir crachá</button>
        </form>
    </main>

    <footer class="rodape">
        {{ config('sms.nome') }} — SisRecepção v{{ config('app.version') }}
    </footer>
</div>
@endsection
