<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acomodações, experiências e serviços salvos nos favoritos.">
    <title>Favoritos | Clone Airbnb</title>
    <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body data-pagina="favoritos">
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <!-- ===== CABEÇALHO ===== -->
    <header class="site-header">
        <div class="container">
            <div class="header-topo">
                <a href="index.php" class="logo" aria-label="Clone Airbnb — página inicial">
                    <img src="img/logos/logo-airbnb.png" alt="" width="100" height="32">
                </a>

                <button type="button" class="busca-abrir" aria-expanded="false" aria-controls="form-busca">
                    <i class="bi bi-search" aria-hidden="true"></i> Comece sua busca
                </button>

                <nav class="abas" aria-label="Categorias">
                    <a href="index.php" class="aba"><i class="bi bi-grid" aria-hidden="true"></i><span>Tudo</span></a>
                    <a href="acomodacoes.php" class="aba"><i class="bi bi-house-door" aria-hidden="true"></i><span>Acomodações</span></a>
                    <a href="experiencias.php" class="aba"><i class="bi bi-balloon" aria-hidden="true"></i><span>Experiências</span></a>
                    <a href="servicos.php" class="aba"><i class="bi bi-bell" aria-hidden="true"></i><span>Serviços</span></a>
                </nav>

                <div class="header-acoes">
                    <a href="anfitriao.php" class="link-anfitriao">Torne-se um anfitrião</a>
                    <button type="button" class="icon-btn" data-acao="tema" aria-pressed="false" aria-label="Alternar tema escuro"><i class="bi bi-moon" aria-hidden="true"></i></button>
                    <div class="menu-usuario">
                        <button type="button" class="menu-toggle" aria-expanded="false" aria-controls="menu-usuario" aria-label="Abrir menu">
                            <i class="bi bi-list" aria-hidden="true"></i><i class="bi bi-person-circle" aria-hidden="true"></i>
                        </button>
                        <nav class="menu-dropdown" id="menu-usuario" aria-label="Menu do usuário" hidden>
                            <a href="login.php" data-menu="entrar"><strong>Entrar</strong></a>
                            <a href="login.php#cadastro" data-menu="cadastrar">Cadastrar-se</a>
                            <div class="separador" role="separator"></div>
                            <a href="favoritos.php">Favoritos <span class="contador contador-favoritos"></span></a>
                            <a href="anfitriao.php">Torne-se um anfitrião</a>
                            <a href="#rodape">Central de Ajuda</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Formulário de busca (GET): envia para a listagem de acomodações -->
            <form class="busca" id="form-busca" action="acomodacoes.php" method="get" role="search" aria-label="Buscar acomodações">
                <div class="busca-campo">
                    <label for="busca-onde">Onde</label>
                    <input type="search" id="busca-onde" name="onde" placeholder="Buscar destinos" autocomplete="off">
                </div>
                <div class="busca-campo">
                    <label for="busca-checkin">Check-in</label>
                    <input type="date" id="busca-checkin" name="checkin">
                </div>
                <div class="busca-campo">
                    <label for="busca-checkout">Checkout</label>
                    <input type="date" id="busca-checkout" name="checkout">
                </div>
                <div class="busca-campo">
                    <label for="busca-hospedes">Quem</label>
                    <input type="number" id="busca-hospedes" name="hospedes" min="1" max="16" placeholder="Hóspedes?">
                </div>
                <button type="submit" class="busca-botao"><i class="bi bi-search" aria-hidden="true"></i><span class="visually-hidden">Buscar</span></button>
            </form>
        </div>
    </header>

    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <div class="resultado-resumo">
            <h1 class="titulo-pagina">Favoritos</h1>
            <button type="button" class="btn btn-secundario btn-pequeno" id="limpar-favoritos">Limpar todos</button>
        </div>
        <p id="resultado-texto" aria-live="polite" class="texto-suave"></p>
        <div id="lista-favoritos" class="grade"></div>
    </main>

    <!-- ===== INSPIRAÇÃO ===== -->
    <section class="inspiracao" aria-labelledby="titulo-inspiracao">
        <div class="container">
            <h2 id="titulo-inspiracao">Inspiração para viagens futuras</h2>
            <div class="inspiracao-abas" role="tablist" aria-label="Tipos de destino"></div>
            <ul class="inspiracao-lista" id="painel-inspiracao" role="tabpanel"></ul>
        </div>
    </section>

    <!-- ===== RODAPÉ ===== -->
    <footer class="rodape" id="rodape">
        <div class="container">
            <div class="rodape-colunas">
                <nav aria-labelledby="rodape-atendimento">
                    <h2 id="rodape-atendimento">Atendimento</h2>
                    <ul>
                        <li><a href="#">Central de Ajuda</a></li>
                        <li><a href="#">AirCover</a></li>
                        <li><a href="#">Antidiscriminação</a></li>
                        <li><a href="#">Apoio à pessoa com deficiência</a></li>
                        <li><a href="#">Opções de cancelamento</a></li>
                    </ul>
                </nav>
                <nav aria-labelledby="rodape-hospedagem">
                    <h2 id="rodape-hospedagem">Hospedagem</h2>
                    <ul>
                        <li><a href="anfitriao.php">Anuncie seu espaço</a></li>
                        <li><a href="anfitriao.php">Anuncie sua experiência</a></li>
                        <li><a href="#">AirCover para anfitriões</a></li>
                        <li><a href="#">Recursos para anfitriões</a></li>
                        <li><a href="#">Hospedagem responsável</a></li>
                    </ul>
                </nav>
                <nav aria-labelledby="rodape-empresa">
                    <h2 id="rodape-empresa">Airbnb</h2>
                    <ul>
                        <li><a href="#">Newsroom</a></li>
                        <li><a href="#">Carreiras</a></li>
                        <li><a href="#">Investidores</a></li>
                        <li><a href="#">Locais emergenciais Airbnb.org</a></li>
                    </ul>
                </nav>
            </div>
            <div class="rodape-base">
                <p>© <span data-ano>2026</span> Clone Airbnb · Privacidade · Termos</p>
                <div class="redes">
                    <span><i class="bi bi-globe" aria-hidden="true"></i> Português (BR)</span>
                    <span>R$ BRL</span>
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    <a href="#" aria-label="X (Twitter)"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                </div>
            </div>
            <p class="aviso-educacional">
                <i class="bi bi-mortarboard" aria-hidden="true"></i>
                Este site é um <strong>clone desenvolvido para fins educacionais</strong> (Desenvolvimento Web I — IFC Campus Concórdia),
                sem qualquer vínculo com a Airbnb, Inc. Todos os usuários, anúncios, avaliações e valores são fictícios.
            </p>
        </div>
    </footer>

    <!-- Modal de detalhes (experiências e serviços) -->
    <dialog class="modal" id="modal-detalhe" aria-labelledby="modal-detalhe-titulo"></dialog>

    <script src="js/dados.js"></script>
    <script src="js/main.js"></script>
    <script src="js/catalogo.js"></script>
</body>
</html>
