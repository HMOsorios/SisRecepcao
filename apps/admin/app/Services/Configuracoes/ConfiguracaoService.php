<?php

namespace App\Services\Configuracoes;

use App\Models\Configuracao;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;

/**
 * Regras de persistência do Painel de Configurações (Seção 8.4) com auditoria.
 */
class ConfiguracaoService
{
    /**
     * Campos institucionais editáveis no painel (chave => rotulo/tipo).
     *
     * @return array<string,array<string,mixed>>
     */
    public function campos(): array
    {
        return [
            'sms_nome' => ['rotulo' => 'Nome da Secretaria', 'tipo' => 'string'],
            'sms_sigla' => ['rotulo' => 'Sigla', 'tipo' => 'string'],
            'sms_identidade' => ['rotulo' => 'Identidade (Prefeitura)', 'tipo' => 'string'],
            'sms_endereco' => ['rotulo' => 'Endereço', 'tipo' => 'string'],
            'sms_telefone' => ['rotulo' => 'Telefone', 'tipo' => 'string'],
            'sms_email' => ['rotulo' => 'E-mail de contato', 'tipo' => 'string'],
            'sis_pec_url' => ['rotulo' => 'URL SisPec', 'tipo' => 'string'],
            'sei_url' => ['rotulo' => 'URL SEI', 'tipo' => 'string'],
            'ti_conecta_url' => ['rotulo' => 'URL TI Conecta', 'tipo' => 'string'],
            'sis_escala_url' => ['rotulo' => 'URL SisEscala', 'tipo' => 'string'],
        ];
    }

    /**
     * Valores atuais de todos os campos.
     *
     * @return array<string,string|null>
     */
    public function valores(): array
    {
        $mapa = [
            'sms_nome' => config('sms.nome'),
            'sms_sigla' => config('sms.sigla'),
            'sms_identidade' => config('sms.identidade'),
            'sms_endereco' => config('sms.contato.endereco'),
            'sms_telefone' => config('sms.contato.telefone'),
            'sms_email' => config('sms.contato.email'),
            'sis_pec_url' => config('sms.sistemas.0.url'),
            'sei_url' => config('sms.sistemas.1.url'),
            'ti_conecta_url' => config('sms.sistemas.2.url'),
            'sis_escala_url' => config('sms.sistemas.3.url'),
        ];

        $gravados = Configuracao::allCached();

        foreach ($mapa as $chave => $padrao) {
            $mapa[$chave] = (string) ($gravados[$chave] ?? $padrao);
        }

        return $mapa;
    }

    /**
     * Persiste o formulário de dados institucionais e audita a alteração.
     *
     * @param  array<string,string|null>  $dados
     */
    public function salvar(array $dados, string $usuarioId, string $usuarioNome, Request $request): void
    {
        $alterados = [];
        $atuais = Configuracao::allCached();

        foreach ($this->campos() as $chave => $meta) {
            $novoValor = (string) ($dados[$chave] ?? '');
            if (($atuais[$chave] ?? null) !== $novoValor) {
                $alterados[$chave] = $novoValor;
                Configuracao::set($chave, $novoValor, $meta['tipo']);
            }
        }

        LogAuditoria::registrar(
            $usuarioId,
            $usuarioNome,
            'configuracoes.salvar',
            ['campos' => array_keys($alterados)],
            $request,
        );
    }

    /**
     * Versão do sistema (rodapé/painel) — config ou valor gravado.
     */
    public function versao(): string
    {
        return (string) (Configuracao::get('app_version') ?? config('app.version', '1.0.0'));
    }
}
