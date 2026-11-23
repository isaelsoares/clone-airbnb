/* =========================================================================
   main.js — Comportamentos comuns a todas as páginas
   - Tema claro/escuro (localStorage)
   - Menu do usuário responsivo (abrir/fechar, Esc, clique fora)
   - Busca recolhível no mobile
   - Favoritos (localStorage) e contador no menu
   - Funções de template para os cards e controles de carrossel
   - Abas "Inspiração para viagens futuras"
   ========================================================================= */

/* ---------- Utilitários ---------- */
const moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
const formatarMoeda = valor => moeda.format(valor);
const formatarNota = nota => nota.toFixed(2).replace('.', ',').replace(/0$/, '');

/* Lê e grava JSON no localStorage com tratamento de erro
   (o navegador pode bloquear o armazenamento em modo privado) */
function lerStorage(chave, padrao) {
    try {
        const valor = localStorage.getItem(chave);
        return valor ? JSON.parse(valor) : padrao;
    } catch (e) {
        return padrao;
    }
}
function gravarStorage(chave, valor) {
    try { localStorage.setItem(chave, JSON.stringify(valor)); } catch (e) { /* ignora */ }
}

/* Evita injeção de HTML ao montar templates com texto */
function escaparHTML(texto) {
    return String(texto).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/* ---------- Tema claro/escuro ---------- */
const CHAVE_TEMA = 'cloneAirbnb.tema';

function aplicarTema(tema) {
    document.documentElement.dataset.theme = tema;
    document.querySelectorAll('[data-acao="tema"]').forEach(botao => {
        const escuro = tema === 'dark';
        botao.setAttribute('aria-pressed', escuro);
        const icone = botao.querySelector('i');
        if (icone) icone.className = escuro ? 'bi bi-sun' : 'bi bi-moon';
        const rotulo = botao.querySelector('.rotulo-tema');
        if (rotulo) rotulo.textContent = escuro ? 'Tema claro' : 'Tema escuro';
    });
}

function iniciarTema() {
    const salvo = lerStorage(CHAVE_TEMA, null);
    const prefereEscuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
    aplicarTema(salvo || (prefereEscuro ? 'dark' : 'light'));

    document.querySelectorAll('[data-acao="tema"]').forEach(botao => {
        botao.addEventListener('click', () => {
            const novo = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            aplicarTema(novo);
            gravarStorage(CHAVE_TEMA, novo);
        });
    });
}

/* ---------- Menu do usuário (dropdown) ---------- */
function iniciarMenu() {
    const botao = document.querySelector('.menu-toggle');
    const menu = document.getElementById('menu-usuario');
    if (!botao || !menu) return;

    const abrir = () => {
        menu.hidden = false;
        botao.setAttribute('aria-expanded', 'true');
        botao.setAttribute('aria-label', 'Fechar menu');
        menu.querySelector('a, button')?.focus();
    };
    const fechar = (devolverFoco = false) => {
        menu.hidden = true;
        botao.setAttribute('aria-expanded', 'false');
        botao.setAttribute('aria-label', 'Abrir menu');
        if (devolverFoco) botao.focus();
    };

    botao.addEventListener('click', () => (menu.hidden ? abrir() : fechar()));

    // fecha ao clicar fora
    document.addEventListener('click', evento => {
        if (!menu.hidden && !menu.contains(evento.target) && !botao.contains(evento.target)) fechar();
    });
    // fecha com a tecla Esc e devolve o foco ao botão
    document.addEventListener('keydown', evento => {
        if (evento.key === 'Escape' && !menu.hidden) fechar(true);
    });

    // Usuário "logado" (simulação da Parte 2 — dados salvos pelo login.js)
    const usuario = lerStorage('cloneAirbnb.usuario', null);
    if (usuario) {
        const icone = botao.querySelector('.bi-person-circle');
        if (icone) icone.outerHTML = `<span class="avatar-letra" aria-hidden="true">${escaparHTML(usuario.nome[0].toUpperCase())}</span>`;
        const entrar = menu.querySelector('[data-menu="entrar"]');
        const cadastrar = menu.querySelector('[data-menu="cadastrar"]');
        if (entrar) entrar.innerHTML = `<strong>Olá, ${escaparHTML(usuario.nome.split(' ')[0])}</strong>`;
        if (cadastrar) {
            const sair = document.createElement('button');
            sair.type = 'button';
            sair.textContent = 'Sair';
            sair.addEventListener('click', () => {
                try { localStorage.removeItem('cloneAirbnb.usuario'); } catch (e) { /* ignora */ }
                window.location.reload();
            });
            cadastrar.replaceWith(sair);
        }
    }
}

/* ---------- Busca recolhível (mobile) ---------- */
function iniciarBuscaMobile() {
    const botao = document.querySelector('.busca-abrir');
    const form = document.querySelector('.busca');
    if (!botao || !form) return;
    botao.addEventListener('click', () => {
        const aberta = form.classList.toggle('aberta');
        botao.setAttribute('aria-expanded', aberta);
        if (aberta) form.querySelector('input')?.focus();
    });

    // Preenche a busca com os parâmetros da URL (GET) e impede checkout < check-in
    const params = new URLSearchParams(window.location.search);
    ['onde', 'checkin', 'checkout', 'hospedes'].forEach(nome => {
        const campo = form.elements[nome];
        if (campo && params.get(nome)) campo.value = params.get(nome);
    });
    const ini = form.elements.checkin, fim = form.elements.checkout;
    if (ini && fim) {
        ini.min = paraISO(hoje());
        fim.min = ini.value || ini.min;
        ini.addEventListener('change', () => {
            fim.min = ini.value || ini.min;
            if (fim.value && fim.value <= ini.value) fim.value = '';
        });
    }
}

/* ---------- Favoritos (localStorage) ---------- */
const CHAVE_FAVORITOS = 'cloneAirbnb.favoritos';
const lerFavoritos = () => lerStorage(CHAVE_FAVORITOS, []);

function ehFavorito(chave) {
    return lerFavoritos().includes(chave);
}

function alternarFavorito(chave) {
    const lista = lerFavoritos();
    const indice = lista.indexOf(chave);
    if (indice >= 0) lista.splice(indice, 1); else lista.push(chave);
    gravarStorage(CHAVE_FAVORITOS, lista);
    atualizarContadorFavoritos();
    document.dispatchEvent(new CustomEvent('favoritos:alterados', { detail: { chave } }));
    return indice < 0;
}

function atualizarContadorFavoritos() {
    const total = lerFavoritos().length;
    document.querySelectorAll('.contador-favoritos').forEach(el => {
        el.textContent = total || '';
    });
}

/* Delegação de eventos: um único listener trata todos os corações da página,
   inclusive os cards criados depois dinamicamente */
function iniciarFavoritos() {
    atualizarContadorFavoritos();
    document.addEventListener('click', evento => {
        const botao = evento.target.closest('.fav-btn');
        if (!botao) return;
        evento.preventDefault();
        const marcado = alternarFavorito(botao.dataset.fav);
        botao.setAttribute('aria-pressed', marcado);
        botao.querySelector('i').className = marcado ? 'bi bi-heart-fill' : 'bi bi-heart';
        botao.setAttribute('aria-label', marcado ? 'Remover dos favoritos' : 'Salvar nos favoritos');
    });
}

/* ---------- Templates de cards ---------- */
function botaoFavorito(chave, nome) {
    const marcado = ehFavorito(chave);
    return `<button type="button" class="fav-btn" data-fav="${chave}" aria-pressed="${marcado}"
        aria-label="${marcado ? 'Remover dos favoritos' : 'Salvar nos favoritos'}: ${escaparHTML(nome)}">
        <i class="bi ${marcado ? 'bi-heart-fill' : 'bi-heart'}" aria-hidden="true"></i></button>`;
}

/* Card de acomodação: leva para anuncio.php?id=N */
function cardAcomodacao(a) {
    return `
    <article class="card">
        <a class="card-link" href="anuncio.php?id=${a.id}">
            <div class="card-midia">
                <img src="img/acomodacoes/${a.imagens[0]}.jpg" alt="Foto de ${escaparHTML(a.titulo)}, ${a.tipo.toLowerCase()} em ${a.cidade}" loading="lazy" width="400" height="400">
                ${a.selo ? `<span class="badge">${a.selo}</span>` : ''}
            </div>
            <h3 class="card-titulo">${a.tipo} · ${a.cidade}</h3>
            <p class="card-info">${escaparHTML(a.titulo)}</p>
            <p class="card-info"><strong>${formatarMoeda(a.diaria)}</strong> noite · <i class="bi bi-star-fill" aria-hidden="true"></i> ${formatarNota(a.nota)}</p>
        </a>
        ${botaoFavorito('acomodacao-' + a.id, a.titulo)}
    </article>`;
}

/* Card de experiência/serviço: abre um modal com os detalhes */
function cardItem(item, tipo) {
    const unidade = tipo === 'experiencia' ? '/pessoa' : '/grupo';
    return `
    <article class="card">
        <button type="button" class="card-link" data-detalhe="${tipo}-${item.id}" style="border:0;background:none;padding:0;text-align:left;width:100%">
            <div class="card-midia">
                <img src="img/${item.imagem}" alt="${escaparHTML(item.alt)}" loading="lazy" width="400" height="400">
                ${item.selo ? `<span class="badge">${item.selo}</span>` : ''}
            </div>
            <h3 class="card-titulo">${escaparHTML(item.titulo)}</h3>
            <p class="card-info">${item.categoria ? item.categoria + ' · ' : ''}${item.cidade}</p>
            <p class="card-info">A partir de <strong>${formatarMoeda(item.preco)}</strong> ${unidade} · <i class="bi bi-star-fill" aria-hidden="true"></i> ${formatarNota(item.nota)}</p>
        </button>
        ${botaoFavorito(tipo + '-' + item.id, item.titulo)}
    </article>`;
}

/* Seção com carrossel horizontal */
function secaoCarrossel(titulo, cardsHTML, opcoes = {}) {
    const id = 'car-' + Math.random().toString(36).slice(2, 8);
    return `
    <section aria-labelledby="${id}-titulo">
        <div class="secao-cabecalho">
            <h2 id="${id}-titulo">${opcoes.link ? `<a href="${opcoes.link}">${titulo} <i class="bi bi-chevron-right" aria-hidden="true"></i></a>` : titulo}</h2>
            <div class="carrossel-controles">
                <button type="button" class="icon-btn" data-carrossel="${id}" data-direcao="-1" aria-label="Itens anteriores de ${titulo}"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
                <button type="button" class="icon-btn" data-carrossel="${id}" data-direcao="1" aria-label="Próximos itens de ${titulo}"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
            </div>
        </div>
        ${opcoes.subtitulo ? `<p class="secao-subtitulo">${opcoes.subtitulo}</p>` : ''}
        <div class="carrossel" id="${id}">${cardsHTML}</div>
    </section>`;
}

/* Liga os botões ‹ › dos carrosséis (delegação de eventos) */
function iniciarCarrosseis() {
    document.addEventListener('click', evento => {
        const botao = evento.target.closest('[data-carrossel]');
        if (!botao) return;
        const trilho = document.getElementById(botao.dataset.carrossel);
        trilho.scrollBy({ left: trilho.clientWidth * Number(botao.dataset.direcao), behavior: 'smooth' });
    });
    atualizarSetasCarrossel();
    document.addEventListener('scroll', evento => {
        if (evento.target.classList && evento.target.classList.contains('carrossel')) atualizarSetasCarrossel(evento.target);
    }, true);
    window.addEventListener('resize', () => atualizarSetasCarrossel());
}

/* Desabilita a seta quando o carrossel está no início/fim */
function atualizarSetasCarrossel(alvo) {
    const trilhos = alvo ? [alvo] : document.querySelectorAll('.carrossel');
    trilhos.forEach(trilho => {
        const [ant, prox] = document.querySelectorAll(`[data-carrossel="${trilho.id}"]`);
        if (!ant) return;
        ant.disabled = trilho.scrollLeft <= 2;
        prox.disabled = trilho.scrollLeft + trilho.clientWidth >= trilho.scrollWidth - 2;
    });
}

/* ---------- Inspiração para viagens futuras (abas acessíveis) ---------- */
function iniciarInspiracao() {
    const abas = document.querySelector('.inspiracao-abas');
    const lista = document.querySelector('.inspiracao-lista');
    if (!abas || !lista) return;

    abas.innerHTML = Object.keys(INSPIRACAO).map((nome, i) =>
        `<button type="button" role="tab" id="aba-insp-${i}" aria-controls="painel-inspiracao" aria-selected="${i === 0}" tabindex="${i === 0 ? 0 : -1}">${nome}</button>`
    ).join('');

    const mostrar = nome => {
        lista.innerHTML = INSPIRACAO[nome].map(([cidade, desc]) =>
            `<li><a href="acomodacoes.php?onde=${encodeURIComponent(cidade)}" style="text-decoration:none"><strong>${cidade}</strong><span class="texto-suave">${desc}</span></a></li>`
        ).join('');
    };
    mostrar(Object.keys(INSPIRACAO)[0]);

    const botoes = [...abas.querySelectorAll('button')];
    const selecionar = botao => {
        botoes.forEach(b => { b.setAttribute('aria-selected', b === botao); b.tabIndex = b === botao ? 0 : -1; });
        lista.setAttribute('aria-labelledby', botao.id);
        mostrar(botao.textContent);
    };
    botoes.forEach((botao, i) => {
        botao.addEventListener('click', () => selecionar(botao));
        // setas do teclado navegam entre as abas
        botao.addEventListener('keydown', evento => {
            const passo = evento.key === 'ArrowRight' ? 1 : evento.key === 'ArrowLeft' ? -1 : 0;
            if (!passo) return;
            const proximo = botoes[(i + passo + botoes.length) % botoes.length];
            proximo.focus();
            selecionar(proximo);
        });
    });
}

/* ---------- Inicialização ---------- */
document.addEventListener('DOMContentLoaded', () => {
    iniciarTema();
    iniciarMenu();
    iniciarBuscaMobile();
    iniciarFavoritos();
    iniciarInspiracao();
    document.querySelectorAll('[data-ano]').forEach(el => { el.textContent = new Date().getFullYear(); });
});
