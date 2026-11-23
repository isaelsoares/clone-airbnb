/* =========================================================================
   anuncio.js — Página de detalhe da acomodação (anuncio.php?id=N)
   - Preenche a página com os dados do anúncio
   - Galeria de fotos em modal com navegação (botões, miniaturas e setas)
   - Calendário de 2 meses para escolher check-in/checkout
   - Formulário de reserva com as regras de negócio RN1, RN2 e RN3
   ========================================================================= */

/* Descrição de cada foto (usada no atributo alt) */
const DESCRICOES_FOTOS = {
    'casa-01': 'Sala clara com sofá branco, almofadas azuis e quadro abstrato',
    'casa-02': 'Sala integrada à cozinha com poltrona vermelha e janelas amplas',
    'casa-03': 'Casa branca com piscina cercada de palmeiras',
    'casa-04': 'Chalé de madeira com janelas iluminadas ao anoitecer',
    'casa-05': 'Casa moderna branca com piscina e plantas tropicais',
    'casa-06': 'Bangalô de palha sobre o mar de águas claras',
    'casa-07': 'Piscina entre coqueiros com montanhas ao fundo',
    'casa-08': 'Chalé de madeira com deck, espreguiçadeiras e piscina',
    'casa-09': 'Piscina iluminada à noite em frente ao hotel',
    'casa-10': 'Casa moderna de dois andares com piscina e gramado',
    'casa-11': 'Fachada moderna branca com piscina retangular',
    'casa-12': 'Casa branca com piscina e espreguiçadeira',
    'casa-13': 'Casa contemporânea com grande árvore no jardim ao entardecer',
    'casa-14': 'Sala com sofá azul, televisão e piso de madeira',
    'casa-15': 'Cozinha branca com bancada e banquetas pretas',
    'casa-16': 'Quarto com cama de casal e bancos laranja',
    'casa-17': 'Cozinha branca com ilha central e luminárias pendentes',
    'casa-18': 'Cabana de madeira no campo ao pôr do sol',
    'casa-19': 'Chalé à beira do lago cercado de pinheiros com neve',
    'casa-20': 'Sala aconchegante com plantas e poltrona',
    'casa-21': 'Sala ampla com sofás bege e janelas grandes',
    'casa-22': 'Casa de tijolos com telhado azul no meio do bosque',
    'casa-23': 'Quarto com cama e portas de madeira abertas para a varanda',
    'casa-24': 'Piscina com palmeiras ao entardecer'
};

/* Coordenadas aproximadas das cidades (para o mapa do OpenStreetMap) */
const COORDENADAS = {
    'Ipojuca': [-8.5050, -35.0050],
    'Arraial do Cabo': [-22.9660, -42.0270],
    'Gramado': [-29.3790, -50.8740],
    'Florianópolis': [-27.5950, -48.5480]
};

const acomodacao = buscarAcomodacao(new URLSearchParams(location.search).get('id')) || ACOMODACOES[0];
const fotos = acomodacao.imagens.map(nome => ({ src: `img/acomodacoes/${nome}.jpg`, alt: DESCRICOES_FOTOS[nome] || acomodacao.titulo }));

/* ---------- Preenchimento da página ---------- */
function preencherAnuncio() {
    const a = acomodacao;
    document.title = `${a.titulo} | Clone Airbnb`;
    document.getElementById('anuncio-titulo').textContent = a.titulo;
    document.getElementById('anuncio-subtitulo').textContent = `${a.tipo}: espaço inteiro em ${a.cidade}, Brasil`;
    document.getElementById('anuncio-resumo').textContent =
        `${a.capacidade} hóspedes · ${a.quartos} quarto(s) · ${a.camas} cama(s) · ${a.banheiros} banheiro(s)`;
    document.getElementById('anuncio-descricao').textContent = a.descricao;
    document.getElementById('anuncio-local').textContent = `${a.cidade}, ${a.estado}, Brasil`;
    document.getElementById('nota-geral').textContent = formatarNota(a.nota);
    document.getElementById('regra-hospedes').textContent = `Máximo de ${a.capacidade} hóspedes`;
    document.getElementById('regra-noites').textContent = `Estadia mínima de ${a.minNoites} noite(s).`;
    document.getElementById('reserva-id').value = a.id;
    document.getElementById('reserva-hospedes').max = a.capacidade; // RN2 no próprio HTML

    document.getElementById('anuncio-destaque').innerHTML = `
        <i class="bi bi-trophy" style="font-size:1.8rem" aria-hidden="true"></i>
        <p style="flex:1">${a.selo ? '<strong>Preferido dos hóspedes.</strong> Uma das acomodações que fazem mais sucesso com os hóspedes.' : 'Acomodação muito bem avaliada pelos hóspedes.'}</p>
        <p class="numero">${formatarNota(a.nota)}<small aria-hidden="true">★★★★★</small></p>
        <p class="numero">${a.avaliacoes}<small>avaliações</small></p>`;

    document.getElementById('anfitriao-mini').innerHTML = `
        <span class="avatar" aria-hidden="true">${a.anfitriao[0]}</span>
        <div><h2 style="font-size:1rem">Anfitrião(ã): ${escaparHTML(a.anfitriao)}</h2>
        <p class="texto-suave">Hospedando desde ${a.anfitriaoDesde}</p></div>`;

    // Galeria: 5 fotos em grid; cada uma abre o modal na posição clicada
    document.getElementById('galeria').insertAdjacentHTML('afterbegin', fotos.map((f, i) => `
        <button type="button" data-foto="${i}" aria-label="Abrir foto ${i + 1} de ${fotos.length}">
            <img src="${f.src}" alt="${f.alt}" ${i ? 'loading="lazy"' : ''}>
        </button>`).join(''));

    document.getElementById('anuncio-comodos').innerHTML = [['Quarto', `${a.camas} cama(s)`, a.imagens.find(n => n === 'casa-16' || n === 'casa-23') || a.imagens[3]],
        ['Sala de estar', '1 sofá-cama', a.imagens.find(n => ['casa-01', 'casa-14', 'casa-20', 'casa-21'].includes(n)) || a.imagens[1]]]
        .map(([nome, desc, img]) => `<div><img src="img/acomodacoes/${img}.jpg" alt="${DESCRICOES_FOTOS[img]}" loading="lazy"><h3>${nome}</h3><p class="texto-suave">${desc}</p></div>`).join('');

    document.getElementById('anuncio-comodidades').innerHTML = a.comodidades.map(c =>
        `<li><i class="bi ${ICONES_COMODIDADES[c] || 'bi-check2'}" aria-hidden="true"></i>${c}</li>`).join('');

    document.getElementById('lista-avaliacoes').innerHTML = AVALIACOES.slice(0, Math.min(6, a.avaliacoes)).map(r => `
        <article class="avaliacao">
            <header><span class="avatar" aria-hidden="true">${r.nome[0]}</span>
                <div><h3>${r.nome}</h3><p class="texto-suave" style="font-size:.875rem">${r.perfil}</p></div></header>
            <p class="estrelas"><span aria-label="5 estrelas">★★★★★</span> · ${r.quando}</p>
            <p>${r.texto}</p>
        </article>`).join('');

    // Mapa do OpenStreetMap (iframe) centralizado na cidade
    const [lat, lon] = COORDENADAS[a.cidade] || [-15.78, -47.93];
    document.getElementById('mapa').src =
        `https://www.openstreetmap.org/export/embed.html?bbox=${lon - .05},${lat - .03},${lon + .05},${lat + .03}&layer=mapnik&marker=${lat},${lon}`;
}

/* ---------- Salvar e compartilhar ---------- */
function iniciarAcoes() {
    const salvar = document.getElementById('btn-salvar');
    const chave = 'acomodacao-' + acomodacao.id;
    const atualizar = () => {
        const marcado = ehFavorito(chave);
        salvar.setAttribute('aria-pressed', marcado);
        salvar.querySelector('i').className = marcado ? 'bi bi-heart-fill' : 'bi bi-heart';
        salvar.querySelector('span').textContent = marcado ? 'Salvo' : 'Salvar';
    };
    salvar.addEventListener('click', () => { alternarFavorito(chave); atualizar(); });
    atualizar();

    const aviso = document.getElementById('aviso-copiado');
    document.getElementById('btn-compartilhar').addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(location.href);
            aviso.querySelector('span').textContent = 'Link copiado para a área de transferência.';
        } catch (e) {
            aviso.querySelector('span').textContent = 'Copie o link da barra de endereços: ' + location.href;
        }
        aviso.hidden = false;
        setTimeout(() => { aviso.hidden = true; }, 3500);
    });
}

/* ---------- Modal da galeria (carrossel) ---------- */
function iniciarGaleria() {
    const modal = document.getElementById('modal-galeria');
    const foto = document.getElementById('galeria-foto');
    const contador = document.getElementById('galeria-contador');
    const miniaturas = document.getElementById('galeria-miniaturas');
    let atual = 0;

    miniaturas.innerHTML = fotos.map((f, i) =>
        `<button type="button" data-ir="${i}" aria-label="Ver foto ${i + 1}"><img src="${f.src}" alt=""></button>`).join('');

    function mostrar(indice) {
        atual = (indice + fotos.length) % fotos.length; // volta ao início depois da última
        foto.src = fotos[atual].src;
        foto.alt = fotos[atual].alt;
        contador.textContent = `${atual + 1} / ${fotos.length} — ${fotos[atual].alt}`;
        miniaturas.querySelectorAll('button').forEach((b, i) => b.setAttribute('aria-current', i === atual));
    }

    function abrir(indice) {
        mostrar(indice);
        modal.showModal();
    }

    document.getElementById('galeria').addEventListener('click', evento => {
        const botao = evento.target.closest('[data-foto]');
        if (botao) abrir(Number(botao.dataset.foto));
    });
    document.getElementById('btn-todas-fotos').addEventListener('click', evento => {
        evento.preventDefault();
        abrir(0);
    });
    document.getElementById('foto-anterior').addEventListener('click', () => mostrar(atual - 1));
    document.getElementById('foto-proxima').addEventListener('click', () => mostrar(atual + 1));
    miniaturas.addEventListener('click', evento => {
        const botao = evento.target.closest('[data-ir]');
        if (botao) mostrar(Number(botao.dataset.ir));
    });
    modal.addEventListener('keydown', evento => {
        if (evento.key === 'ArrowLeft') mostrar(atual - 1);
        if (evento.key === 'ArrowRight') mostrar(atual + 1);
    });
    modal.addEventListener('click', evento => {
        if (evento.target.closest('[data-fechar]') || evento.target === modal) modal.close();
    });
}

/* ---------- Calendário + formulário de reserva ---------- */
function iniciarReserva() {
    const form = document.getElementById('form-reserva');
    const campoIni = form.elements.checkin;
    const campoFim = form.elements.checkout;
    const campoHosp = form.elements.hospedes;
    const calendario = document.getElementById('calendario');
    const titulo = document.getElementById('titulo-calendario');
    const dica = document.getElementById('calendario-dica');
    const caixaErros = document.getElementById('reserva-erros');
    const tabela = document.getElementById('tabela-preco');
    const tituloReserva = document.getElementById('titulo-reserva');
    let mesInicial = new Date(hoje().getFullYear(), hoje().getMonth(), 1);

    campoIni.min = paraISO(hoje());
    const params = new URLSearchParams(location.search);
    if (params.get('checkin')) campoIni.value = params.get('checkin');
    if (params.get('checkout')) campoFim.value = params.get('checkout');
    if (params.get('hospedes')) campoHosp.value = params.get('hospedes');

    /* Desenha um mês (grid de 7 colunas) */
    function desenharMes(base) {
        const nomeMes = base.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
        const diasNoMes = new Date(base.getFullYear(), base.getMonth() + 1, 0).getDate();
        const ini = paraData(campoIni.value), fim = paraData(campoFim.value);
        let html = `<div class="mes"><h3>${nomeMes}</h3><div class="mes-grade" role="grid" aria-label="${nomeMes}">`;
        html += ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'].map(d => `<span class="semana" aria-hidden="true">${d}</span>`).join('');
        html += '<span></span>'.repeat(base.getDay()); // espaços antes do dia 1
        for (let d = 1; d <= diasNoMes; d++) {
            const data = new Date(base.getFullYear(), base.getMonth(), d);
            const passado = data < hoje();
            const selecionado = (ini && +data === +ini) || (fim && +data === +fim);
            const noIntervalo = ini && fim && data > ini && data < fim;
            html += `<button type="button" class="dia${selecionado ? ' selecionado' : ''}${noIntervalo ? ' no-intervalo' : ''}"
                data-data="${paraISO(data)}" ${passado ? 'disabled' : ''} aria-pressed="${!!selecionado}"
                aria-label="${data.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long' })}">${d}</button>`;
        }
        return html + '</div></div>';
    }

    function desenharCalendario() {
        const seguinte = new Date(mesInicial.getFullYear(), mesInicial.getMonth() + 1, 1);
        calendario.innerHTML = desenharMes(mesInicial) + desenharMes(seguinte);
        document.getElementById('mes-anterior').disabled = mesInicial <= new Date(hoje().getFullYear(), hoje().getMonth(), 1);

        const noites = contarNoites(campoIni.value, campoFim.value);
        if (campoIni.value && campoFim.value && noites > 0) {
            titulo.textContent = `${noites} noite(s) em ${acomodacao.cidade}`;
            dica.textContent = `${paraData(campoIni.value).toLocaleDateString('pt-BR')} – ${paraData(campoFim.value).toLocaleDateString('pt-BR')}`;
        } else {
            titulo.textContent = campoIni.value ? 'Selecione a data de checkout' : 'Selecione a data de check-in';
            dica.textContent = `Estadia mínima: ${acomodacao.minNoites} noite(s)`;
        }
    }

    /* Atualiza a tabela de preços e mostra os erros das regras de negócio */
    function atualizarResumo(mostrarErros = false) {
        const erros = validarReserva(acomodacao, campoIni.value, campoFim.value, campoHosp.value);
        [campoIni, campoFim, campoHosp].forEach(c =>
            c.setAttribute('aria-invalid', mostrarErros && erros.some(e => e.campo === c.name)));
        caixaErros.innerHTML = mostrarErros ? erros.map(e => `<p class="erro-campo">${e.msg}</p>`).join('') : '';

        const noites = contarNoites(campoIni.value, campoFim.value);
        if (erros.length || noites < 1) {
            tabela.hidden = true;
            tituloReserva.innerHTML = `${formatarMoeda(acomodacao.diaria)} <span>noite</span>`;
            return erros;
        }
        const r = calcularReserva(acomodacao, noites);
        tabela.querySelector('tbody').innerHTML = `
            <tr><td>${noites} noite(s) x ${formatarMoeda(acomodacao.diaria)}</td><td>${formatarMoeda(r.subtotal)}</td></tr>
            ${r.desconto ? `<tr class="desconto"><td>Desconto semanal (10%)</td><td>−${formatarMoeda(r.desconto)}</td></tr>` : ''}
            <tr><td>Taxa de limpeza</td><td>${formatarMoeda(r.limpeza)}</td></tr>
            <tr><td>Taxa de serviço</td><td>${formatarMoeda(r.servico)}</td></tr>`;
        document.getElementById('preco-total').textContent = formatarMoeda(r.total);
        tabela.hidden = false;
        tituloReserva.innerHTML = `${formatarMoeda(r.total)} <span>por ${noites} noite(s)</span>`;
        return erros;
    }

    /* Clique no dia: 1º clique = check-in, 2º clique (data posterior) = checkout */
    calendario.addEventListener('click', evento => {
        const dia = evento.target.closest('.dia');
        if (!dia) return;
        const data = dia.dataset.data;
        if (!campoIni.value || campoFim.value || data <= campoIni.value) {
            campoIni.value = data;
            campoFim.value = '';
        } else {
            campoFim.value = data;
        }
        desenharCalendario();
        atualizarResumo(!!campoFim.value);
        calendario.querySelector(`[data-data="${data}"]`)?.focus();
    });

    document.getElementById('mes-anterior').addEventListener('click', () => {
        mesInicial = new Date(mesInicial.getFullYear(), mesInicial.getMonth() - 1, 1);
        desenharCalendario();
    });
    document.getElementById('mes-seguinte').addEventListener('click', () => {
        mesInicial = new Date(mesInicial.getFullYear(), mesInicial.getMonth() + 1, 1);
        desenharCalendario();
    });
    document.getElementById('limpar-datas').addEventListener('click', () => {
        campoIni.value = campoFim.value = '';
        desenharCalendario();
        atualizarResumo();
    });

    // os campos de data e o calendário ficam sincronizados
    [campoIni, campoFim, campoHosp].forEach(campo => campo.addEventListener('change', () => {
        campoFim.min = campoIni.value || campoIni.min;
        desenharCalendario();
        atualizarResumo(true);
    }));

    form.addEventListener('submit', evento => {
        const erros = atualizarResumo(true);
        if (erros.length) {
            evento.preventDefault(); // não vai para o checkout com dados inválidos
            form.elements[erros[0].campo].focus();
        }
    });

    desenharCalendario();
    atualizarResumo(!!(campoIni.value && campoFim.value));
}

document.addEventListener('DOMContentLoaded', () => {
    preencherAnuncio();
    iniciarAcoes();
    iniciarGaleria();
    iniciarReserva();
});
