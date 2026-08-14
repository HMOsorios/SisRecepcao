/* SisRecepção — Console do atendente (Módulo 2.4).
   Fila via API + atualização em tempo real via Mercure (SSE) com fallback
   de polling. Ações: próxima senha, chamar, iniciar, encerrar, redirecionar,
   status, notificações do setor. */
(function () {
    'use strict';

    var CONFIG = {};
    var els = {};
    var state = { fila: [] };

    function lerConfig() {
        var meta = document.querySelector('meta[name="console"]');
        if (!meta) { return; }
        CONFIG = {
            filaUrl: meta.dataset.filaUrl,
            mercureUrl: meta.dataset.mercureUrl,
            unidadeId: Number(meta.dataset.unidadeId),
            departamentoId: meta.dataset.departamentoId,
            poll: Number(meta.dataset.poll) || 8000,
        };
    }

    function init() {
        lerConfig();
        els = {
            fila: document.querySelector('[data-fila]'),
            proxima: document.querySelector('[data-proxima]'),
            erro: document.querySelector('[data-erro]'),
            local: document.querySelector('#local'),
            numeroLocal: document.querySelector('#numeroLocal'),
            notificacoes: document.querySelector('[data-notificacoes]'),
        };

        if (!els.fila) { return; }

        els.proxima.addEventListener('click', chamarProxima);
        document.querySelectorAll('[data-status]').forEach(function (b) {
            b.addEventListener('click', function () { alterarStatus(b.dataset.status); });
        });

        buscarFila();
        setInterval(buscarFila, CONFIG.poll);
        conectarSSE();
        carregarNotificacoes();
    }

    // Escapa texto vindo do visitante (nome/observação digitados no totem)
    // antes de injetar em innerHTML — evita XSS armazenado (Seção 8.8).
    function escapeHtml(valor) {
        return String(valor == null ? '' : valor).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function headers() {
        return { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' };
    }

    function post(url, body) {
        return fetch(url, { method: 'POST', headers: headers(), body: body ? JSON.stringify(body) : undefined })
            .then(function (r) {
                if (!r.ok) { return r.json().then(function (b) { throw new Error(b.error || b.message || 'Erro ' + r.status); }); }
                return r.json();
            });
    }

    function buscarFila() {
        fetch(CONFIG.filaUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { if (!r.ok) { throw new Error('HTTP ' + r.status); } return r.json(); })
            .then(function (res) {
                state.fila = res.fila || [];
                renderizarFila();
            })
            .catch(function (e) { mostrarErro('Falha ao carregar fila: ' + e.message); });
    }

    function renderizarFila() {
        if (!state.fila.length) {
            els.fila.innerHTML = '<li style="color:var(--cor-texto-suave);">Nenhuma senha na fila.</li>';
            return;
        }

        els.fila.innerHTML = state.fila.map(function (t) {
            var peso = (t.prioridade && t.prioridade.peso) ? Number(t.prioridade.peso) : 0;
            var senha = t.senha
                ? (typeof t.senha === 'object' ? (t.senha.sigla || '') + ' ' + String(t.senha.numero).padStart(2, '0') : t.senha)
                : ('#' + t.id);
            var servico = escapeHtml(t.servico ? (t.servico.nome || t.servico) : '');
            var nome = escapeHtml(t.cliente && t.cliente.nome ? t.cliente.nome : '');

            return '<li class="fila__item' + (peso > 0 ? ' fila__item--prioridade' : '') + '">' +
                '<span class="fila__senha">' + escapeHtml(senha) + '</span>' +
                '<div class="fila__info">' +
                    '<div><strong>' + servico + '</strong>' + (nome ? ' — ' + nome : '') + '</div>' +
                    '<div><span class="fila__status">' + escapeHtml((t.status || '').replace(/_/g, ' ')) + '</span>' +
                    (t.prioridade ? ' <span style="color:' + escapeHtml(t.prioridade.cor) + ';">' + escapeHtml(t.prioridade.nome) + '</span>' : '') + '</div>' +
                '</div>' +
                '<div class="fila__acoes">' +
                    '<button class="botao" data-acao="chamar" data-id="' + t.id + '">Chamar</button>' +
                    '<button class="botao botao--contorno" data-acao="iniciar" data-id="' + t.id + '">Iniciar</button>' +
                    '<button class="botao botao--contorno" data-acao="encerrar" data-id="' + t.id + '">Encerrar</button>' +
                '</div>' +
            '</li>';
        }).join('');

        els.fila.querySelectorAll('[data-acao]').forEach(function (b) {
            b.addEventListener('click', function () {
                var acao = b.dataset.acao;
                var id = b.dataset.id;
                if (acao === 'chamar') { chamar(id); }
                else if (acao === 'iniciar') { iniciar(id); }
                else if (acao === 'encerrar') { encerrar(id); }
            });
        });
    }

    function guiche() {
        return { local: Number(els.local.value), numeroLocal: Number(els.numeroLocal.value || 0) };
    }

    function chamarProxima() {
        post('/atendente/proxima', guiche())
            .then(function (res) { mostrarSucesso('Senha ' + senhaLabel(res.atendimento) + ' chamada.'); buscarFila(); })
            .catch(function (e) { mostrarErro(e.message); });
    }

    function chamar(id) {
        post('/atendente/chamar', Object.assign({ id: Number(id) }, guiche()))
            .then(function (res) { mostrarSucesso('Senha chamada para o guichê.'); buscarFila(); })
            .catch(function (e) { mostrarErro(e.message); });
    }

    function iniciar(id) {
        post('/atendente/iniciar/' + id)
            .then(function () { mostrarSucesso('Atendimento iniciado.'); buscarFila(); })
            .catch(function (e) { mostrarErro(e.message); });
    }

    function encerrar(id) {
        if (!confirm('Encerrar este atendimento?')) { return; }
        post('/atendente/encerrar/' + id)
            .then(function () { mostrarSucesso('Atendimento encerrado.'); buscarFila(); })
            .catch(function (e) { mostrarErro(e.message); });
    }

    function alterarStatus(status) {
        post('/atendente/status', { status: status })
            .then(function () { mostrarSucesso('Status atualizado: ' + status.replace(/_/g, ' ').toLowerCase()); })
            .catch(function (e) { mostrarErro(e.message); });
    }

    function conectarSSE() {
        if (!CONFIG.mercureUrl) { return; }
        try {
            var sse = new EventSource(CONFIG.mercureUrl);
            sse.onmessage = function () { buscarFila(); };
        } catch (e) { console.warn('[atendente] SSE:', e.message); }
    }

    function carregarNotificacoes() {
        if (!CONFIG.departamentoId) { return; }
        fetch('/atendente/notificacoes/' + CONFIG.departamentoId, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                var itens = res.naoLidas || [];
                if (!itens.length) {
                    els.notificacoes.innerHTML = '<p style="color:var(--cor-texto-suave);">Nenhuma notificação pendente.</p>';
                    return;
                }
                els.notificacoes.innerHTML = itens.map(function (n) {
                    var p = n.payload || {};
                    return '<div class="notificacao"><div class="notificacao__titulo">👤 Visitante chegou</div>' +
                        '<div>' + escapeHtml(p.nome || p.visitor || 'Visitante') + '</div>' +
                        '<div>' + escapeHtml(p.observacao || '') + '</div></div>';
                }).join('');
            })
            .catch(function () {});
    }

    function senhaLabel(atendimento) {
        if (!atendimento) { return ''; }
        var s = atendimento.senha;
        return (typeof s === 'object' ? (s.sigla || '') + ' ' + String(s.numero).padStart(2, '0') : s);
    }

    function mostrarErro(msg) {
        els.erro.textContent = msg;
        els.erro.hidden = false;
    }

    function mostrarSucesso(msg) {
        els.erro.hidden = true;
        console.info('[atendente]', msg);
    }

    document.addEventListener('DOMContentLoaded', init);
})();
