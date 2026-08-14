/* SisRecepção — Painel de chamada (Módulo 2.2).
   Atualização em tempo real via Mercure (EventSource/SSE) do NovoSGA com
   fallback automático para polling a cada 8s (Seção 2.6 passo 4). */
(function () {
    'use strict';

    var CONFIG = {};

    function lerConfig() {
        var meta = document.querySelector('meta[name="painel"]');
        if (!meta) { return; }
        CONFIG = {
            dadosUrl: meta.dataset.dadosUrl,
            mercureUrl: meta.dataset.mercureUrl,
            poll: Number(meta.dataset.poll) || 8000,
            reconnect: Number(meta.dataset.reconnect) || 5000,
        };
    }

    var state = {
        featured: null,
        history: [],
        eventSource: null,
        sseRetries: 0,
        pollTimer: null,
        reconnectTimer: null,
    };

    var els = {};

    function init() {
        lerConfig();
        els = {
            relogio: document.querySelector('[data-relogio]'),
            senha: document.querySelector('[data-senha]'),
            guiche: document.querySelector('[data-guiche]'),
            cliente: document.querySelector('[data-cliente]'),
            historico: document.querySelector('[data-historico]'),
            painel: document.querySelector('[data-painel]'),
        };

        atualizarRelogio();
        setInterval(atualizarRelogio, 1000);

        conectarSSE();
        buscarDados();
    }

    function atualizarRelogio() {
        if (!els.relogio) { return; }
        var agora = new Date();
        var p = function (n) { return String(n).padStart(2, '0'); };
        els.relogio.innerHTML = '<strong>' + p(agora.getHours()) + ':' + p(agora.getMinutes()) + ':' + p(agora.getSeconds()) + '</strong>' +
            agora.toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: 'long' });
    }

    function chaveDedupe(t) {
        return [t.sigla, t.numero, t.local, t.numeroLocal, t.servico].join('|');
    }

    function buscarDados() {
        fetch(CONFIG.dadosUrl)
            .then(function (r) { if (!r.ok) { throw new Error('HTTP ' + r.status); } return r.json(); })
            .then(function (dados) {
                if (state.featured === null) {
                    // Carga inicial
                    state.featured = dados[0] || null;
                    state.history = dados.slice(1, 9);
                } else {
                    var novos = dados.filter(function (t) { return t.id > state.featured.id; });
                    if (novos.length) {
                        novos.slice().reverse().forEach(function (rec) {
                            if (state.featured && chaveDedupe(rec) !== chaveDedupe(state.featured)) {
                                state.history = [state.featured].concat(state.history.filter(function (h) { return chaveDedupe(h) !== chaveDedupe(rec); })).slice(0, 9);
                            }
                            state.featured = rec;
                        });
                        tocarAlerta();
                    }
                }
                renderizar();
            })
            .catch(function (e) { console.warn('[painel] fetchData:', e.message); });
    }

    function renderizar() {
        if (!els.senha) { return; }
        if (!state.featured) {
            els.senha.textContent = '---';
            els.guiche.textContent = '';
            els.cliente.textContent = '';
            return;
        }

        var t = state.featured;
        els.senha.textContent = t.senha || (t.sigla + ' ' + String(t.numero).padStart(2, '0'));
        els.senha.classList.toggle('painel__chamada-senha--prioridade', t.peso > 0);
        els.guiche.textContent = (t.local ? t.local + ' ' : '') + (t.numeroLocal ? String(t.numeroLocal).padStart(2, '0') : '');
        els.cliente.textContent = t.cliente || t.servico || '';

        els.historico.innerHTML = state.history
            .map(function (h) {
                return '<div class="painel__hist-item"><div class="painel__hist-senha">' +
                    (h.senha || h.sigla + ' ' + String(h.numero).padStart(2, '0')) +
                    '</div><div>' + (h.local || '') + ' ' + (h.numeroLocal || '') + '</div></div>';
            })
            .join('');
    }

    function tocarAlerta() {
        try {
            var AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) { return; }
            var ctx = new AudioCtx();
            var tons = [
                { freq: 880, inicio: 0, dur: 0.18, ganho: 0.45 },
                { freq: 660, inicio: 0.2, dur: 0.28, ganho: 0.35 },
            ];
            tons.forEach(function (t) {
                var osc = ctx.createOscillator();
                var env = ctx.createGain();
                var agora = ctx.currentTime;
                osc.type = 'sine';
                osc.frequency.setValueAtTime(t.freq, agora + t.inicio);
                env.gain.setValueAtTime(0, agora + t.inicio);
                env.gain.linearRampToValueAtTime(t.ganho, agora + t.inicio + 0.02);
                env.gain.linearRampToValueAtTime(0, agora + t.inicio + t.dur);
                osc.connect(env);
                env.connect(ctx.destination);
                osc.start(agora + t.inicio);
                osc.stop(agora + t.inicio + t.dur);
            });
        } catch (e) { console.warn('[painel] alerta:', e.message); }
    }

    function conectarSSE() {
        desconectar();

        if (!CONFIG.mercureUrl) {
            console.warn('[painel] Mercure não configurado; usando polling');
            iniciarPolling();
            return;
        }

        try {
            state.eventSource = new EventSource(CONFIG.mercureUrl);
            state.eventSource.onopen = function () { state.sseRetries = 0; };
            state.eventSource.onmessage = function () {
                state.sseRetries = 0;
                buscarDados();
            };
            state.eventSource.onerror = function () {
                state.sseRetries++;
                if (state.sseRetries >= 3) {
                    console.warn('[painel] SSE falhou; caindo para polling');
                    desconectar();
                    iniciarPolling();
                } else {
                    state.reconnectTimer = setTimeout(conectarSSE, CONFIG.reconnect);
                }
            };
        } catch (e) {
            console.warn('[painel] EventSource:', e.message);
            iniciarPolling();
        }
    }

    function iniciarPolling() {
        buscarDados();
        state.pollTimer = setInterval(buscarDados, CONFIG.poll);
        setTimeout(function () {
            if (state.pollTimer) {
                state.sseRetries = 0;
                conectarSSE();
            }
        }, 60000);
    }

    function desconectar() {
        if (state.eventSource) { state.eventSource.close(); state.eventSource = null; }
        if (state.pollTimer) { clearInterval(state.pollTimer); state.pollTimer = null; }
        if (state.reconnectTimer) { clearTimeout(state.reconnectTimer); state.reconnectTimer = null; }
    }

    document.addEventListener('DOMContentLoaded', init);
})();
