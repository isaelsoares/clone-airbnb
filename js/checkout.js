/* =========================================================================
   checkout.js — "Confirmar e pagar"
   Recebe a reserva pela URL (GET vindo do anuncio.php), revalida as regras
   de negócio, monta o resumo e valida o formulário de pagamento.
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(location.search);
    const acomodacao = buscarAcomodacao(params.get('id')) || ACOMODACOES[0];

    // valores padrão caso a página seja aberta direto, sem vir do anúncio
    const inicioPadrao = new Date(hoje().getTime() + 14 * 86400000);
    const fimPadrao = new Date(inicioPadrao.getTime() + Math.max(acomodacao.minNoites, 2) * 86400000);
    const reserva = {
        checkin: params.get('checkin') || paraISO(inicioPadrao),
        checkout: params.get('checkout') || paraISO(fimPadrao),
        hospedes: Number(params.get('hospedes')) || 1
    };
    const linkAnuncio = `anuncio.php?id=${acomodacao.id}&checkin=${reserva.checkin}&checkout=${reserva.checkout}&hospedes=${reserva.hospedes}`;
    ['voltar-anuncio', 'alterar-datas', 'alterar-hospedes'].forEach(id => { document.getElementById(id).href = linkAnuncio; });

    /* ---------- Resumo ---------- */
    const noites = contarNoites(reserva.checkin, reserva.checkout);
    const valores = calcularReserva(acomodacao, Math.max(noites, 0));
    const dataBR = iso => paraData(iso).toLocaleDateString('pt-BR', { day: 'numeric', month: 'short', year: 'numeric' });

    const foto = document.getElementById('resumo-foto');
    foto.src = `img/acomodacoes/${acomodacao.imagens[0]}.jpg`;
    foto.alt = `Foto principal de ${acomodacao.titulo}`;
    document.getElementById('titulo-resumo').textContent = acomodacao.titulo;
    document.getElementById('resumo-nota').innerHTML = `<i class="bi bi-star-fill" aria-hidden="true"></i> ${formatarNota(acomodacao.nota)} (${acomodacao.avaliacoes}) · ${acomodacao.cidade}`;
    document.getElementById('resumo-datas').textContent = `${dataBR(reserva.checkin)} – ${dataBR(reserva.checkout)}`;
    document.getElementById('resumo-hospedes').textContent = `${reserva.hospedes} hóspede(s)`;
    document.querySelector('#tabela-checkout tbody').innerHTML = `
        <tr><td>${noites} noite(s) x ${formatarMoeda(acomodacao.diaria)}</td><td>${formatarMoeda(valores.subtotal)}</td></tr>
        ${valores.desconto ? `<tr class="desconto"><td>Desconto semanal (10%)</td><td>−${formatarMoeda(valores.desconto)}</td></tr>` : ''}
        <tr><td>Taxa de limpeza</td><td>${formatarMoeda(valores.limpeza)}</td></tr>
        <tr><td>Taxa de serviço</td><td>${formatarMoeda(valores.servico)}</td></tr>`;
    document.getElementById('checkout-total').textContent = formatarMoeda(valores.total);
    document.getElementById('texto-parcelas').textContent = `ou em 12x de ${formatarMoeda(valores.total / 12)}`;

    /* As regras de negócio são verificadas de novo aqui (a URL pode ter sido editada) */
    const form = document.getElementById('form-pagamento');
    const errosReserva = validarReserva(acomodacao, reserva.checkin, reserva.checkout, reserva.hospedes);
    if (errosReserva.length) {
        const resumo = form.querySelector('[data-resumo]');
        resumo.hidden = false;
        resumo.querySelector('span').innerHTML = errosReserva.map(e => e.msg).join(' ') +
            ` <a href="${linkAnuncio}">Voltar e alterar a reserva</a>`;
        document.getElementById('btn-confirmar').disabled = true;
    }

    /* ---------- Forma de pagamento ---------- */
    const camposCartao = document.getElementById('campos-cartao');
    const parcelas = document.getElementById('pag-parcelas');
    const etapaParcelas = document.getElementById('etapa-parcelas');
    parcelas.innerHTML = Array.from({ length: 12 }, (_, i) =>
        `<option value="${i + 1}">${i + 1}x de ${formatarMoeda(valores.total / (i + 1))} sem juros</option>`).join('');

    form.querySelectorAll('input[name="forma"]').forEach(radio => radio.addEventListener('change', () => {
        const cartao = form.elements.forma.value === 'cartao';
        camposCartao.hidden = !cartao;          // campos ocultos não são validados
        parcelas.disabled = !cartao;
        etapaParcelas.classList.toggle('ativa', cartao);
        document.getElementById('dica-parcelas').textContent = cartao
            ? 'Escolha em quantas vezes deseja pagar.' : 'Disponível apenas para cartão de crédito.';
        if (cartao) form.elements.cartao.focus();
    }));

    /* ---------- Limites dos campos ---------- */
    form.elements.validade.min = paraISO(hoje()).slice(0, 7); // AAAA-MM do mês atual
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
    const intervalo = setInterval(atualizarRelogio, 1000);

    /* ---------- Validação e confirmação ---------- */
    validarFormulario(form, {
        regras: {
            cpf: valor => cpfValido(valor) ? '' : 'CPF inválido. Confira os números digitados.'
        },
        aoEnviar: f => {
            if (errosReserva.length) return;
            const codigo = 'HM' + Math.random().toString(36).slice(2, 8).toUpperCase();
            const reservas = lerStorage('cloneAirbnb.reservas', []);
            reservas.push({ codigo, id: acomodacao.id, ...reserva, total: valores.total, forma: f.elements.forma.value });
            gravarStorage('cloneAirbnb.reservas', reservas);

            clearInterval(intervalo);
            document.getElementById('checkout-colunas').hidden = true;
            const confirmacao = document.getElementById('confirmacao');
            document.getElementById('texto-confirmacao').innerHTML =
                `Código <strong>${codigo}</strong> · ${escaparHTML(acomodacao.titulo)}<br>` +
                `${dataBR(reserva.checkin)} – ${dataBR(reserva.checkout)} · ${formatarMoeda(valores.total)}` +
                (f.elements.forma.value === 'cartao' ? ` em ${f.elements.parcelas.value}x` : ' via Pix') +
                `<br>Enviamos os detalhes para ${escaparHTML(f.elements.email.value)} (simulação).`;
            confirmacao.hidden = false;
            confirmacao.focus();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    aplicarMascaras();
});
