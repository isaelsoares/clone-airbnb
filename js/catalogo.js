/* =========================================================================
   catalogo.js — Conteúdo dinâmico das páginas de catálogo
   (Início, Acomodações, Experiências, Serviços e Favoritos)
   Os cards são gerados a partir dos arrays de objetos de js/dados.js.
   ========================================================================= */

/* Remove acentos e deixa minúsculo, para a busca ignorar "ã", "é" etc. */
const normalizar = texto => texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

/* Agrupa um array por uma propriedade: { cidade: [itens] } */
function agruparPor(lista, prop) {
    return lista.reduce((grupos, item) => {
        (grupos[item[prop]] = grupos[item[prop]] || []).push(item);
        return grupos;
    }, {});
}

const parametros = new URLSearchParams(window.location.search);

/* ---------- Página inicial ---------- */
function renderInicio() {
    const porCidade = agruparPor(ACOMODACOES.filter(a => a.tipo !== 'Hotel'), 'cidade');
    const titulos = ['Acomodações perto da praia · %', 'Acomodações muito procuradas em %', 'Fique em %', 'Lugares para ficar em %'];
    let html = Object.entries(porCidade).map(([cidade, itens], i) =>
        secaoCarrossel(titulos[i % titulos.length].replace('%', cidade), itens.map(cardAcomodacao).join(''),
            { link: `acomodacoes.html?onde=${encodeURIComponent(cidade)}` })
    ).join('');

    const hoteis = ACOMODACOES.filter(a => a.categorias.includes('hotel'));
    html += secaoCarrossel('Ótimos hotéis para sua próxima viagem', hoteis.map(cardAcomodacao).join(''),
        { subtitulo: 'Além disso, ganhe créditos ao ficar em um hotel em destaque.', link: 'acomodacoes.html?categoria=hotel' });
    document.getElementById('secoes-dinamicas').innerHTML = html;

    renderTilesServicos(categoria => `servicos.html?categoria=${encodeURIComponent(categoria)}`);

    document.getElementById('lista-destinos').innerHTML = DESTINOS.map(d => `
        <a class="destino" href="acomodacoes.html?onde=${encodeURIComponent(d.nome)}">
            <img src="img/${d.imagem}" alt="${d.alt}" loading="lazy" width="300" height="225">
            <h3>${d.nome}</h3>
            <p>${d.desc}</p>
        </a>`).join('');

    document.getElementById('secao-experiencias').innerHTML = secaoCarrossel(
        'Capture momentos inesquecíveis perto de você',
        EXPERIENCIAS.map(e => cardItem(e, 'experiencia')).join(''), { link: 'experiencias.html' });
}

/* Tiles das categorias de serviço (sem repetir categoria) */
function renderTilesServicos(gerarLink) {
    const alvo = document.getElementById('tiles-servicos');
    if (!alvo) return;
    const vistos = new Set();
    alvo.innerHTML = SERVICOS.filter(s => !vistos.has(s.categoria) && vistos.add(s.categoria)).map(s => `
        <a class="tile" href="${gerarLink(s.categoria)}" data-categoria-servico="${s.categoria}">
            <img src="img/${s.imagem}" alt="" loading="lazy" width="140" height="140">
            <span>${s.categoria}</span>
        </a>`).join('');
}

/* ---------- Acomodações: busca + filtros ---------- */
function renderAcomodacoes() {
    const form = document.getElementById('form-filtros');
    const chips = [...document.querySelectorAll('#filtro-categorias .chip')];
    const faixa = document.getElementById('filtro-preco');
    const saidaPreco = document.getElementById('saida-preco');
    const ordem = document.getElementById('filtro-ordem');
    const resultados = document.getElementById('resultados');
    const texto = document.getElementById('resultado-texto');

    // estado inicial vem da URL (formulário de busca do cabeçalho usa GET)
    const estado = {
        onde: parametros.get('onde') || '',
        hospedes: Number(parametros.get('hospedes')) || 0,
        categoria: parametros.get('categoria') || '',
        precoMax: Number(faixa.max),
        ordem: 'relevancia'
    };

    function marcarChip() {
        chips.forEach(c => c.setAttribute('aria-pressed', c.dataset.categoria === estado.categoria));
    }

    function aplicar() {
        let lista = ACOMODACOES.filter(a => {
            const termo = normalizar(estado.onde);
            const casaTexto = !termo || normalizar(`${a.cidade} ${a.estado} ${a.titulo} ${a.tipo}`).includes(termo);
            const casaHospedes = !estado.hospedes || a.capacidade >= estado.hospedes;  // RN2
            const casaCategoria = !estado.categoria || a.categorias.includes(estado.categoria);
            return casaTexto && casaHospedes && casaCategoria && a.diaria <= estado.precoMax;
        });

        if (estado.ordem === 'menor-preco') lista.sort((a, b) => a.diaria - b.diaria);
        if (estado.ordem === 'maior-preco') lista.sort((a, b) => b.diaria - a.diaria);
        if (estado.ordem === 'nota') lista.sort((a, b) => b.nota - a.nota);

        const filtrando = estado.onde || estado.hospedes || estado.categoria ||
            estado.precoMax < Number(faixa.max) || estado.ordem !== 'relevancia';

        // texto de resumo (lido por leitores de tela via aria-live)
        const partes = [];
        if (estado.onde) partes.push(`em “${escaparHTML(estado.onde)}”`);
        if (estado.hospedes) partes.push(`para ${estado.hospedes} hóspede(s)`);
        texto.innerHTML = `<strong>${lista.length}</strong> acomodação(ões) encontrada(s) ${partes.join(' ')}`;

        if (!lista.length) {
            resultados.innerHTML = `<div class="vazio"><i class="bi bi-search" aria-hidden="true"></i>
                Nenhuma acomodação corresponde à sua busca. Tente remover alguns filtros.</div>`;
        } else if (filtrando) {
            resultados.innerHTML = `<div class="grade">${lista.map(cardAcomodacao).join('')}</div>`;
        } else {
            // sem filtros: carrosséis agrupados por cidade, como no wireframe
            resultados.innerHTML = Object.entries(agruparPor(lista, 'cidade')).map(([cidade, itens]) =>
                secaoCarrossel(`Acomodações em ${cidade}`, itens.map(cardAcomodacao).join(''))).join('');
            atualizarSetasCarrossel();
        }
    }

    chips.forEach(chip => chip.addEventListener('click', () => {
        estado.categoria = chip.dataset.categoria;
        marcarChip();
        aplicar();
    }));
    faixa.addEventListener('input', () => {
        estado.precoMax = Number(faixa.value);
        saidaPreco.textContent = formatarMoeda(estado.precoMax);
        aplicar();
    });
    ordem.addEventListener('change', () => { estado.ordem = ordem.value; aplicar(); });
    form.addEventListener('reset', () => {
        Object.assign(estado, { onde: '', hospedes: 0, categoria: '', precoMax: Number(faixa.max), ordem: 'relevancia' });
        saidaPreco.textContent = formatarMoeda(estado.precoMax);
        const busca = document.getElementById('form-busca');
        if (busca) busca.reset();
        history.replaceState(null, '', 'acomodacoes.html');
        marcarChip();
        setTimeout(aplicar); // espera o reset nativo terminar
    });

    marcarChip();
    aplicar();
}

/* ---------- Experiências ---------- */
function renderExperiencias() {
    const termo = normalizar(parametros.get('onde') || '');
    const lista = EXPERIENCIAS.filter(e => !termo || normalizar(`${e.cidade} ${e.titulo}`).includes(termo));
    const alvo = document.getElementById('secoes-dinamicas');
    const card = e => cardItem(e, 'experiencia');

    if (termo) document.getElementById('resultado-texto').textContent = `${lista.length} experiência(s) para “${parametros.get('onde')}”`;
    if (!lista.length) {
        alvo.innerHTML = '<div class="vazio"><i class="bi bi-balloon" aria-hidden="true"></i>Nenhuma experiência encontrada.</div>';
        return;
    }

    let html = '';
    const originais = lista.filter(e => e.selo === 'Original');
    if (originais.length) html += secaoCarrossel('Originais Airbnb', originais.map(card).join(''),
        { subtitulo: 'Oferecidos pelas pessoas mais interessantes do mundo' });
    html += '<h2 class="titulo-pagina">Popular entre viajantes da sua região</h2>';
    html += Object.entries(agruparPor(lista, 'cidade')).map(([cidade, itens]) =>
        secaoCarrossel(`Experiências em ${cidade}`, itens.map(card).join(''))).join('');
    alvo.innerHTML = html;
}

/* ---------- Serviços ---------- */
function renderServicos() {
    let categoria = parametros.get('categoria') || '';
    const termo = normalizar(parametros.get('onde') || '');

    // os tiles funcionam como filtro de categoria (clicar de novo remove o filtro)
    renderTilesServicos(cat => `servicos.html?categoria=${encodeURIComponent(cat)}`);
    const tiles = [...document.querySelectorAll('[data-categoria-servico]')];

    function aplicar() {
        tiles.forEach(t => t.setAttribute('aria-current', t.dataset.categoriaServico === categoria ? 'true' : 'false'));
        tiles.forEach(t => { t.style.opacity = !categoria || t.dataset.categoriaServico === categoria ? 1 : .45; });

        const lista = SERVICOS.filter(s =>
            (!categoria || s.categoria === categoria) &&
            (!termo || normalizar(`${s.cidade} ${s.titulo} ${s.categoria}`).includes(termo)));

        const resumo = document.getElementById('resultado-texto');
        resumo.textContent = categoria || termo ? `${lista.length} serviço(s) encontrado(s)${categoria ? ' em ' + categoria : ''}` : '';

        const card = s => cardItem(s, 'servico');
        document.getElementById('secoes-dinamicas').innerHTML = lista.length
            ? `<h2 class="titulo-pagina">Descubra serviços no Airbnb</h2>` +
              Object.entries(agruparPor(lista, 'cidade')).map(([cidade, itens]) =>
                secaoCarrossel(`Serviços em ${cidade}`, itens.map(card).join(''))).join('')
            : '<div class="vazio"><i class="bi bi-bell" aria-hidden="true"></i>Nenhum serviço encontrado.</div>';

        // tabela de preços (ordenada do mais barato para o mais caro)
        document.getElementById('tabela-servicos').innerHTML = [...lista].sort((a, b) => a.preco - b.preco).map(s => `
            <tr>
                <th scope="row">${escaparHTML(s.titulo)}</th>
                <td>${s.categoria}</td>
                <td>${s.cidade}</td>
                <td class="num"><i class="bi bi-star-fill" aria-hidden="true"></i> ${formatarNota(s.nota)}</td>
                <td class="num">${formatarMoeda(s.preco)}</td>
            </tr>`).join('');
        atualizarSetasCarrossel();
    }

    tiles.forEach(tile => tile.addEventListener('click', evento => {
        evento.preventDefault();
        categoria = categoria === tile.dataset.categoriaServico ? '' : tile.dataset.categoriaServico;
        history.replaceState(null, '', categoria ? `servicos.html?categoria=${encodeURIComponent(categoria)}` : 'servicos.html');
        aplicar();
    }));
    aplicar();
}

/* ---------- Favoritos ---------- */
function renderFavoritos() {
    const alvo = document.getElementById('lista-favoritos');
    const texto = document.getElementById('resultado-texto');

    function desenhar() {
        const cards = lerFavoritos().map(chave => {
            const [tipo, id] = chave.split('-');
            if (tipo === 'acomodacao') { const a = buscarAcomodacao(id); return a && cardAcomodacao(a); }
            const fonte = tipo === 'experiencia' ? EXPERIENCIAS : SERVICOS;
            const item = fonte.find(x => x.id === Number(id));
            return item && cardItem(item, tipo);
        }).filter(Boolean);

        texto.textContent = cards.length ? `${cards.length} item(ns) salvo(s) neste navegador.` : '';
        alvo.innerHTML = cards.length ? cards.join('') : `
            <div class="vazio" style="grid-column:1/-1"><i class="bi bi-heart" aria-hidden="true"></i>
            Você ainda não salvou nada. Toque no coração de um anúncio para guardá-lo aqui.<br><br>
            <a href="index.html" class="btn btn-escuro">Começar a explorar</a></div>`;
        document.getElementById('limpar-favoritos').hidden = !cards.length;
    }

    document.addEventListener('favoritos:alterados', desenhar);
    document.getElementById('limpar-favoritos').addEventListener('click', () => {
        gravarStorage(CHAVE_FAVORITOS, []);
        atualizarContadorFavoritos();
        desenhar();
    });
    desenhar();
}

/* ---------- Modal de detalhes (experiências e serviços) ---------- */
function iniciarModalDetalhe() {
    const modal = document.getElementById('modal-detalhe');
    if (!modal) return;

    document.addEventListener('click', evento => {
        const gatilho = evento.target.closest('[data-detalhe]');
        if (!gatilho) return;
        const [tipo, id] = gatilho.dataset.detalhe.split('-');
        const item = (tipo === 'experiencia' ? EXPERIENCIAS : SERVICOS).find(x => x.id === Number(id));
        if (!item) return;

        modal.innerHTML = `
            <div class="modal-cabecalho">
                <h2 id="modal-detalhe-titulo">${escaparHTML(item.titulo)}</h2>
                <button type="button" class="icon-btn btn-fechar" data-fechar aria-label="Fechar"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
            </div>
            <div class="modal-corpo">
                <img src="img/${item.imagem}" alt="${escaparHTML(item.alt)}" style="border-radius:12px;aspect-ratio:16/10;object-fit:cover;width:100%">
                <p class="texto-suave">${item.categoria ? item.categoria + ' · ' : ''}${item.cidade} · <i class="bi bi-star-fill" aria-hidden="true"></i> ${formatarNota(item.nota)}</p>
                <p>${escaparHTML(item.descricao)}</p>
                <p><strong>${formatarMoeda(item.preco)}</strong> ${tipo === 'experiencia' ? 'por pessoa' : 'por grupo'}</p>
            </div>
            <div class="modal-rodape"><button type="button" class="btn btn-primario btn-bloco" data-fechar>Fechar</button></div>`;
        modal.showModal(); // <dialog> nativo: prende o foco e fecha com Esc
        gatilho.dataset.ultimoFoco = '1';
        modal.querySelector('[data-fechar]').focus();
    });

    modal.addEventListener('click', evento => {
        // fecha no botão "Fechar" ou ao clicar no fundo escurecido
        if (evento.target.closest('[data-fechar]') || evento.target === modal) modal.close();
    });
    modal.addEventListener('close', () => {
        const origem = document.querySelector('[data-ultimo-foco]');
        if (origem) { origem.focus(); delete origem.dataset.ultimoFoco; }
    });
}

/* ---------- Inicialização conforme a página ---------- */
document.addEventListener('DOMContentLoaded', () => {
    const renderizadores = {
        inicio: renderInicio,
        acomodacoes: renderAcomodacoes,
        experiencias: renderExperiencias,
        servicos: renderServicos,
        favoritos: renderFavoritos
    };
    const pagina = document.body.dataset.pagina;
    if (renderizadores[pagina]) renderizadores[pagina]();
    iniciarCarrosseis();
    iniciarModalDetalhe();
});
