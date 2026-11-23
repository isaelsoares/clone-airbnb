/* =========================================================================
   checkout.js — "Confirmar e pagar"
   O resumo, o total e as regras de negócio vêm do checkout.php (servidor).
   Aqui ficam a troca da forma de pagamento, a contagem regressiva e a
   validação no cliente antes de enviar o formulário.
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-pagamento');
    if (!form) return; // página de confirmação: não há formulário

    /* ---------- Forma de pagamento ---------- */
    const camposCartao = document.getElementById('campos-cartao');
    const parcelas = document.getElementById('pag-parcelas');
    const etapaParcelas = document.getElementById('etapa-parcelas');

    function atualizarForma(focar) {
        const cartao = form.elements.forma.value === 'cartao';
        camposCartao.hidden = !cartao;          // campos ocultos não são validados
        parcelas.disabled = !cartao;
        etapaParcelas.classList.toggle('ativa', cartao);
        document.getElementById('dica-parcelas').textContent = cartao
            ? 'Escolha em quantas vezes deseja pagar.' : 'Disponível apenas para cartão de crédito.';
        if (cartao && focar) form.elements.cartao.focus();
    }
    form.querySelectorAll('input[name="forma"]').forEach(radio =>
        radio.addEventListener('change', () => atualizarForma(true)));

    /* ---------- Limites dos campos ---------- */
    form.elements.nascimento.max = dataMaxima18();

    /* ---------- Contagem regressiva (4 horas a partir da 1ª visita) ---------- */
    const chaveTempo = 'cloneAirbnb.prazoReserva';
    let prazo = Number(sessionStorage.getItem(chaveTempo));
    if (!prazo || prazo < Date.now()) {
        prazo = Date.now() + 4 * 3600 * 1000;
        try { sessionStorage.setItem(chaveTempo, prazo); } catch (e) { /* ignora */ }
    }
    const relogio = document.getElementById('tempo-restante');
    const atualizarRelogio = () => {
        const resto = Math.max(0, prazo - Date.now());
        const h = Math.floor(resto / 3600000), m = Math.floor(resto / 60000) % 60, s = Math.floor(resto / 1000) % 60;
        relogio.textContent = `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    };
    atualizarRelogio();
    setInterval(atualizarRelogio, 1000);

    /* ---------- Validação e envio ---------- */
    validarFormulario(form, {
        regras: {
            cpf: valor => cpfValido(valor) ? '' : 'CPF inválido. Confira os números digitados.'
        },
        // dados válidos no cliente: o PHP valida de novo, calcula o total e grava a reserva
        aoEnviar: f => {
            document.getElementById('btn-confirmar').disabled = true;
            try { sessionStorage.removeItem(chaveTempo); } catch (e) { /* ignora */ }
            f.submit();
        }
    });

    aplicarMascaras();
});
