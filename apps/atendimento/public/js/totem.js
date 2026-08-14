/* SisRecepção — Totem de autoatendimento (Módulo 2.1).
   JS vanilla, modo kiosk, com leitura em áudio (Web Speech API). */
(function () {
    'use strict';

    var estado = {
        passo: 1,
        servico: null,
        prioridade: null,
        audio: true,
    };

    var els = {};

    function init() {
        els = {
            relogio: document.querySelector('[data-relogio]'),
            passo: document.querySelectorAll('[data-passo]'),
            servicos: document.querySelector('[data-servicos]'),
            prioridades: document.querySelector('[data-prioridades]'),
            painelServicos: document.querySelector('[data-painel-servicos]'),
            painelPrioridades: document.querySelector('[data-painel-prioridades]'),
            painelDados: document.querySelector('[data-painel-dados]'),
            form: document.querySelector('[data-form-emissao]'),
            servicoInput: document.querySelector('[data-servico-input]'),
            prioridadeInput: document.querySelector('[data-prioridade-input]'),
            btnVoltar: document.querySelector('[data-voltar]'),
            btnAudio: document.querySelector('[data-audio]'),
            btnEmitir: document.querySelector('[data-emitir]'),
            nome: document.querySelector('[data-nome]'),
            documento: document.querySelector('[data-documento]'),
            honeypot: document.querySelector('[data-honeypot]'),
            tempoField: document.querySelector('[data-tempo]'),
            erro: document.querySelector('[data-erro]'),
        };

        if (!els.form) { return; }

        els.form.addEventListener('submit', emitir);
        if (els.btnVoltar) { els.btnVoltar.addEventListener('click', voltar); }
        if (els.btnAudio) { els.btnAudio.addEventListener('click', alternarAudio); }

        atualizarRelogio();
        setInterval(atualizarRelogio, 1000);

        lerAudio('Bem-vindo à Secretaria Municipal de Saúde. Escolha o serviço desejado na tela.');
    }

    function atualizarRelogio() {
        if (!els.relogio) { return; }
        var agora = new Date();
        var p = function (n) { return String(n).padStart(2, '0'); };
        els.relogio.innerHTML = '<strong>' + p(agora.getHours()) + ':' + p(agora.getMinutes()) + '</strong><br>' +
            agora.toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
    }

    function irPara(passo) {
        estado.passo = passo;
        els.passo.forEach(function (el) {
            el.hidden = Number(el.dataset.passo) !== passo;
        });
    }

    function escolherServico(id, el) {
        estado.servico = id;
        els.painelServicos.querySelectorAll('.totem__servico').forEach(function (b) { b.classList.remove('totem__servico--ativo'); });
        if (el) { el.classList.add('totem__servico--ativo'); }
        els.servicoInput.value = id;
    }

    function escolherPrioridade(id, el) {
        estado.prioridade = id;
        els.painelPrioridades.querySelectorAll('.totem__prioridade').forEach(function (b) { b.classList.remove('totem__prioridade--ativo'); });
        if (el) { el.classList.add('totem__prioridade--ativo'); }
        els.prioridadeInput.value = id;
    }

    window.escolherServico = escolherServico;
    window.escolherPrioridade = escolherPrioridade;
    window.irPara = irPara;

    function voltar() {
        if (estado.passo === 2) { irPara(1); }
        else if (estado.passo === 3) { irPara(2); }
    }

    function alternarAudio() {
        estado.audio = !estado.audio;
        if (els.btnAudio) {
            els.btnAudio.textContent = estado.audio ? 'Áudio: ligado' : 'Áudio: desligado';
        }
    }

    function lerAudio(texto) {
        if (!estado.audio || !('speechSynthesis' in window)) { return; }
        window.speechSynthesis.cancel();
        var u = new SpeechSynthesisUtterance(texto);
        u.lang = 'pt-BR';
        u.rate = 1.05;
        window.speechSynthesis.speak(u);
    }

    function emitir(e) {
        e.preventDefault();
        if (!estado.servico || !estado.prioridade) {
            mostrarErro('Selecione o serviço e a prioridade.');
            return;
        }

        els.erro.hidden = true;
        els.btnEmitir.disabled = true;
        els.btnEmitir.textContent = 'Emitindo senha…';

        // honeypot anti-bot (Seção 8.8)
        if (els.honeypot) { els.honeypot.value = ''; }
        if (els.tempoField) { els.tempoField.value = String(Math.floor(Date.now() / 1000)); }

        var dados = new FormData(els.form);
        fetch(els.form.action, {
            method: 'POST',
            body: dados,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
            .then(function (r) {
                if (!r.ok) {
                    return r.json()
                        .catch(function () { throw new Error('Sessão expirada — recarregue a página e tente novamente.'); })
                        .then(function (b) { throw new Error(b.message || 'Falha ao emitir.'); });
                }
                return r.json();
            })
            .then(function (res) {
                var id = res.ticket.id || res.ticket.senha_provisoria;
                window.location.href = '/totem/ticket/' + encodeURIComponent(id) + '?provisional=' + (res.provisional ? '1' : '0');
            })
            .catch(function (err) {
                mostrarErro(err.message);
                els.btnEmitir.disabled = false;
                els.btnEmitir.textContent = 'Emitir senha';
            });
    }

    function mostrarErro(msg) {
        els.erro.textContent = msg;
        els.erro.hidden = false;
        lerAudio(msg);
    }

    document.addEventListener('DOMContentLoaded', init);
})();
