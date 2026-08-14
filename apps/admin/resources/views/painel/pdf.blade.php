<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório da Diretoria — SisRecepção</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        h2 { font-size: 14px; margin: 24px 0 8px; border-bottom: 2px solid #0e6e5e; padding-bottom: 4px; }
        .muted { color: #6b7280; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #0e6e5e; color: #fff; }
        .num { text-align: right; }
        .kpis { width: 100%; margin-top: 10px; }
        .kpis td { border: 1px solid #d1d5db; text-align: center; font-size: 16px; font-weight: bold; }
        .kpis .label { display: block; font-size: 10px; font-weight: normal; color: #6b7280; }
        .footer { margin-top: 28px; font-size: 10px; color: #6b7280; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #0e6e5e; padding-bottom: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>{{ config('sms.nome') }}</h1>
            <div class="muted">{{ config('sms.identidade') }}</div>
        </div>
        <div class="muted">SisRecepção v{{ config('app.version') }}<br>gerado em {{ $geradoEm->format('d/m/Y H:i') }}</div>
    </div>

    <h2>Indicadores gerais</h2>
    <table class="kpis">
        <tr>
            <td>Atendimentos hoje<br><span class="label">{{ $resumo['total_dia'] }}</span></td>
            <td>Em fila agora<br><span class="label">{{ $resumo['em_fila'] }}</span></td>
            <td>Espera média<br><span class="label">{{ $resumo['tempo_medio_espera_s'] !== null ? gmdate('i\m', $resumo['tempo_medio_espera_s']) : '—' }}</span></td>
            <td>Atendimento médio<br><span class="label">{{ $resumo['tempo_medio_atendimento_s'] !== null ? gmdate('i\m', $resumo['tempo_medio_atendimento_s']) : '—' }}</span></td>
            <td>Não compareceu<br><span class="label">{{ $resumo['nao_compareceu'] }}</span></td>
        </tr>
    </table>

    <h2>Atendimentos por setor</h2>
    <table>
        <thead>
            <tr><th>Setor/Serviço</th><th class="num">Atendimentos</th></tr>
        </thead>
        <tbody>
            @forelse ($porSetor as $item)
                <tr><td>{{ $item['setor'] }}</td><td class="num">{{ $item['total'] }}</td></tr>
            @empty
                <tr><td colspan="2">Sem dados disponíveis.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Situação das senhas</h2>
    <table>
        <thead>
            <tr><th>Status</th><th class="num">Quantidade</th></tr>
        </thead>
        <tbody>
            @foreach ($porStatus as $item)
                <tr><td>{{ $item['rotulo'] }}</td><td class="num">{{ $item['total'] }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento gerado automaticamente pelo SisRecepção. Indicadores consolidados a partir
        dos atendimentos registrados no NovoSGA.
    </div>
</body>
</html>
