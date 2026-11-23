/* =========================================================================
   dados.js — Dados fictícios do clone e regras de negócio
   Experiências, serviços e destinos continuam em arrays de objetos; as
   acomodações vêm do banco de dados (cadastradas no painel administrativo).
   ========================================================================= */

/* Comodidades possíveis: nome -> ícone do Bootstrap Icons */
const ICONES_COMODIDADES = {
    'Cozinha': 'bi-egg-fried',
    'Wi-Fi': 'bi-wifi',
    'Estacionamento gratuito': 'bi-car-front',
    'Piscina': 'bi-water',
    'TV': 'bi-tv',
    'Ar-condicionado': 'bi-snow',
    'Lareira': 'bi-fire',
    'Banheira de hidromassagem': 'bi-droplet',
    'Vista para o mar': 'bi-tsunami',
    'Lava-louças': 'bi-moisture',
    'Espaço de trabalho': 'bi-laptop',
    'Detector de fumaça': 'bi-bell'
};

/* Acomodações: lidas do MySQL pelo PHP e entregues como a constante
   ACOMODACOES (veja includes/rodape.php e o painel em admin/) */

/* Experiências */
const EXPERIENCIAS = [
    { id: 1, titulo: 'Jantar com chef em casa histórica', cidade: 'Gramado', preco: 280, nota: 4.97, imagem: 'experiencias/exp-01.jpg', alt: 'Mesa de jantar com prato gourmet e taças de vinho', selo: 'Original', descricao: 'Menu degustação de 6 tempos com harmonização, preparado por um chef local.' },
    { id: 2, titulo: 'Passeio pelas vilas à beira-mar', cidade: 'Arraial do Cabo', preco: 150, nota: 4.9, imagem: 'experiencias/exp-02.jpg', alt: 'Casas brancas na encosta com vista para o mar azul', selo: 'Original', descricao: 'Caminhada guiada pelas ruelas e mirantes, terminando com banho em praia escondida.' },
    { id: 3, titulo: 'Trilha guiada nos cânions', cidade: 'Gramado', preco: 190, nota: 4.95, imagem: 'experiencias/exp-03.jpg', alt: 'Pessoa com mochila caminhando em trilha entre montanhas verdes', selo: 'Em alta', descricao: 'Trilha de nível moderado com guia credenciado e lanche regional.' },
    { id: 4, titulo: 'Road trip de kombi pelo litoral', cidade: 'Florianópolis', preco: 230, nota: 4.88, imagem: 'experiencias/exp-04.jpg', alt: 'Kombi amarela em estrada no deserto entre rochas vermelhas', selo: null, descricao: 'Um dia percorrendo praias do norte da ilha a bordo de uma kombi restaurada.' },
    { id: 5, titulo: 'Nascer do sol acima das nuvens', cidade: 'Gramado', preco: 170, nota: 4.92, imagem: 'experiencias/exp-05.jpg', alt: 'Picos de montanha acima de um mar de nuvens ao amanhecer', selo: 'Em alta', descricao: 'Saída de madrugada até o mirante mais alto da serra, com café quente.' },
    { id: 6, titulo: 'Voo de balão ao amanhecer', cidade: 'Florianópolis', preco: 690, nota: 4.99, imagem: 'experiencias/exp-06.jpg', alt: 'Casal fotografando dezenas de balões coloridos no céu', selo: 'Original', descricao: 'Voo de 1 hora com brinde em solo e certificado de voo.' },
    { id: 7, titulo: 'Passeio de barco em águas cristalinas', cidade: 'Arraial do Cabo', preco: 120, nota: 4.85, imagem: 'experiencias/exp-07.jpg', alt: 'Proa de barco de madeira em lago verde cercado de montanhas', selo: null, descricao: 'Passeio de 4 horas com paradas para mergulho na Praia do Farol.' },
    { id: 8, titulo: 'Acampamento na floresta', cidade: 'Ipojuca', preco: 260, nota: 4.8, imagem: 'experiencias/exp-08.jpg', alt: 'Vista de dentro de uma barraca aberta para a floresta', selo: null, descricao: 'Noite de camping com fogueira, observação de estrelas e café da manhã.' }
];

/* Serviços */
const SERVICOS = [
    { id: 1, titulo: 'Ensaio fotográfico na praia', categoria: 'Fotografia', cidade: 'Florianópolis', preco: 350, nota: 5.0, imagem: 'servicos/serv-01.jpg', alt: 'Mulher sorrindo de jaqueta jeans em frente a uma parede azul', selo: 'Popular', descricao: 'Sessão de 1 hora com 30 fotos editadas entregues em até 3 dias.' },
    { id: 2, titulo: 'Massagem relaxante no quarto', categoria: 'Massagens', cidade: 'Gramado', preco: 220, nota: 4.96, imagem: 'servicos/serv-02.jpg', alt: 'Massagista aplicando óleo nas costas de uma pessoa deitada', selo: 'Popular', descricao: 'Massagem de 60 minutos com óleos essenciais, no conforto da sua hospedagem.' },
    { id: 3, titulo: 'Treino funcional ao ar livre', categoria: 'Treinos', cidade: 'Arraial do Cabo', preco: 90, nota: 4.9, imagem: 'servicos/serv-03.jpg', alt: 'Mulher fazendo abdominais em tapete diante de janela clara', selo: null, descricao: 'Aula de 45 minutos com personal trainer, para todos os níveis.' },
    { id: 4, titulo: 'Motorista particular e traslado', categoria: 'Aluguel de carros', cidade: 'Ipojuca', preco: 180, nota: 4.87, imagem: 'servicos/serv-04.jpg', alt: 'Mão de motorista ao volante de um carro ao entardecer', selo: null, descricao: 'Traslado aeroporto–hospedagem ou diária com motorista bilíngue.' },
    { id: 5, titulo: 'Chef particular para a família', categoria: 'Chefs', cidade: 'Florianópolis', preco: 420, nota: 4.98, imagem: 'servicos/serv-05.jpg', alt: 'Três pessoas cozinhando juntas em uma cozinha', selo: 'Popular', descricao: 'Chef prepara jantar completo para até 6 pessoas usando ingredientes locais.' },
    { id: 6, titulo: 'Fotografia de casamento e eventos', categoria: 'Fotografia', cidade: 'Gramado', preco: 1200, nota: 4.94, imagem: 'servicos/serv-06.jpg', alt: 'Noiva segurando buquê de flores em ambiente escuro', selo: null, descricao: 'Cobertura de até 6 horas com álbum digital.' }
];

/* Destinos para você */
const DESTINOS = [
    { nome: 'Nova York', desc: 'Vida urbana', imagem: 'destinos/dest-01.jpg', alt: 'Horizonte de Nova York com o Empire State Building' },
    { nome: 'Times Square', desc: 'Teatros e compras', imagem: 'destinos/dest-02.jpg', alt: 'Rua movimentada da Times Square com telões' },
    { nome: 'Chicago', desc: 'Arquitetura', imagem: 'destinos/dest-03.jpg', alt: 'Arranha-céus de Chicago vistos do alto' },
    { nome: 'Rio de Janeiro', desc: 'Destino de praia', imagem: 'destinos/dest-04.jpg', alt: 'Pão de Açúcar e baía de Botafogo no Rio de Janeiro' },
    { nome: 'Floresta da Tijuca', desc: 'Natureza', imagem: 'destinos/dest-05.jpg', alt: 'Montanhas verdes e mar vistos do alto da Floresta da Tijuca' },
    { nome: 'Manhattan', desc: 'Vida noturna', imagem: 'destinos/dest-06.jpg', alt: 'Vista aérea noturna de Manhattan iluminada' }
];

/* Inspiração para viagens futuras (abas do rodapé das páginas de catálogo) */
const INSPIRACAO = {
    'Popular': [['Bauru', 'Aluguéis de casas'], ['Santarém', 'Locações com piscina'], ['Uberlândia', 'Aluguéis de chalés'], ['Governador Celso Ramos', 'Locações com piscina'], ['Cunha', 'Locações com piscina'], ['Macapá', 'Aluguéis por temporada'], ['Gramado', 'Aluguéis de casas'], ['Pirenópolis', 'Aluguéis de apartamentos'], ['Ilha de Itaparica', 'Aluguéis de casas'], ['Arraial do Cabo', 'Aluguéis de casas'], ['Águas de Lindóia', 'Aluguéis por temporada'], ['São Bento do Sapucaí', 'Aluguéis de chalés']],
    'Litoral': [['Ubatuba', 'Casas de praia'], ['Búzios', 'Aluguéis por temporada'], ['Porto Seguro', 'Pousadas'], ['Bombinhas', 'Apartamentos'], ['Maragogi', 'Casas de praia'], ['Ilhabela', 'Chalés'], ['Jericoacoara', 'Pousadas'], ['Paraty', 'Casas históricas']],
    'Histórica': [['Ouro Preto', 'Casas coloniais'], ['Tiradentes', 'Pousadas'], ['Olinda', 'Aluguéis por temporada'], ['São Luís', 'Apartamentos'], ['Goiás Velho', 'Casas'], ['Diamantina', 'Pousadas']],
    'Ilhas': [['Fernando de Noronha', 'Pousadas'], ['Ilha Grande', 'Chalés'], ['Ilha do Mel', 'Pousadas'], ['Morro de São Paulo', 'Casas de praia'], ['Florianópolis', 'Casas'], ['Boipeba', 'Bangalôs']],
    'Ao ar livre': [['Chapada dos Veadeiros', 'Cabanas'], ['Bonito', 'Pousadas'], ['Lençóis', 'Chalés'], ['Urubici', 'Cabanas'], ['Cambará do Sul', 'Chalés'], ['Capitólio', 'Casas']]
};

/* Avaliações fictícias exibidas no anúncio */
const AVALIACOES = [
    { nome: 'Miriam', perfil: '10 meses no Airbnb', quando: '1 semana atrás', texto: 'Próximo ao centro, amei tudo organizado.' },
    { nome: 'Rafaela', perfil: '7 meses no Airbnb', quando: '1 semana atrás', texto: 'Muito aconchegante, bem localizado, tudo muito arrumado, limpo e confortável.' },
    { nome: 'Rubem', perfil: '4 anos no Airbnb', quando: 'outubro de 2026', texto: 'Nossa experiência foi maravilhosa! O lugar é exatamente como nas fotos, super aconchegante, limpo e muito bem equipado.' },
    { nome: 'Gabriela', perfil: '3 anos no Airbnb', quando: 'setembro de 2026', texto: 'Lugar incrível, igual às fotos. Assim que chegamos notamos que estava tudo extremamente limpo e cheiroso.' },
    { nome: 'Bruno', perfil: '6 anos no Airbnb', quando: 'setembro de 2026', texto: 'Uma das melhores hospedagens da minha vida. A anfitriã respondeu tudo muito rápido.' },
    { nome: 'Sandra', perfil: '8 anos no Airbnb', quando: 'agosto de 2026', texto: 'Muito bem localizado, com restaurantes ao lado e perto da feira de artesanato, lojas e bares.' }
];

/* =========================================================================
   REGRAS DE NEGÓCIO (reaproveitadas pelo anúncio e pelo checkout)
   RN1 — O checkout deve ser posterior ao check-in, o check-in não pode ser
         no passado e a estadia deve respeitar o mínimo de noites do anúncio.
   RN2 — O número de hóspedes não pode ultrapassar a capacidade do anúncio.
   RN3 — Estadias de 7 noites ou mais recebem 10% de desconto nas diárias.
   ========================================================================= */
const TAXA_LIMPEZA = 120;       // valor fixo por reserva
const TAXA_SERVICO = 0.14;      // 14% sobre as diárias (após desconto)
const DESCONTO_SEMANAL = 0.10;  // RN3
const NOITES_DESCONTO = 7;

/* Converte "AAAA-MM-DD" em Date local (sem problemas de fuso) */
function paraData(texto) {
    if (!texto) return null;
    const [a, m, d] = texto.split('-').map(Number);
    return new Date(a, m - 1, d);
}

/* Converte Date em "AAAA-MM-DD" (formato aceito pelo <input type="date">) */
function paraISO(data) {
    const m = String(data.getMonth() + 1).padStart(2, '0');
    const d = String(data.getDate()).padStart(2, '0');
    return `${data.getFullYear()}-${m}-${d}`;
}

function hoje() {
    const h = new Date();
    return new Date(h.getFullYear(), h.getMonth(), h.getDate());
}

/* Valida a reserva e devolve a lista de erros (vazia = reserva válida) */
function validarReserva(acomodacao, checkin, checkout, hospedes) {
    const erros = [];
    const ini = paraData(checkin);
    const fim = paraData(checkout);

    if (!ini) erros.push({ campo: 'checkin', msg: 'Informe a data de check-in.' });
    else if (ini < hoje()) erros.push({ campo: 'checkin', msg: 'O check-in não pode ser em uma data passada.' });

    if (!fim) erros.push({ campo: 'checkout', msg: 'Informe a data de checkout.' });
    else if (ini && fim <= ini) erros.push({ campo: 'checkout', msg: 'O checkout deve ser depois do check-in.' });
    else if (ini && contarNoites(checkin, checkout) < acomodacao.minNoites) {
        erros.push({ campo: 'checkout', msg: `Este anúncio exige no mínimo ${acomodacao.minNoites} noite(s).` });
    }

    const qtd = Number(hospedes);
    if (!qtd || qtd < 1) erros.push({ campo: 'hospedes', msg: 'Informe ao menos 1 hóspede.' });
    else if (qtd > acomodacao.capacidade) {
        erros.push({ campo: 'hospedes', msg: `Esta acomodação comporta no máximo ${acomodacao.capacidade} hóspedes.` });
    }
    return erros;
}

function contarNoites(checkin, checkout) {
    const ini = paraData(checkin), fim = paraData(checkout);
    if (!ini || !fim) return 0;
    return Math.round((fim - ini) / 86400000);
}

/* Calcula os valores da reserva (usado na tabela de preço) */
function calcularReserva(acomodacao, noites) {
    const subtotal = acomodacao.diaria * noites;
    const desconto = noites >= NOITES_DESCONTO ? subtotal * DESCONTO_SEMANAL : 0;
    const servico = (subtotal - desconto) * TAXA_SERVICO;
    const total = subtotal - desconto + TAXA_LIMPEZA + servico;
    return { noites, subtotal, desconto, limpeza: TAXA_LIMPEZA, servico, total };
}

function buscarAcomodacao(id) {
    return ACOMODACOES.find(a => a.id === Number(id)) || null;
}
