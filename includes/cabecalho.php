<?php
/* =========================================================================
   cabecalho.php — <head>, abertura do <body> e cabeçalho do site
   Variáveis que a página define antes do include:
     $titulo         texto da aba do navegador
     $descricao      meta description
     $pagina         valor do data-pagina (usado pelos scripts)
     $cabecalho      'completo' (abas + busca), 'compacto' (logo + menu),
                     'simples' (só o logo), 'anfitriao' ou 'admin' (painel)
     $aba            aba marcada nos modos completo e admin
     $busca          destino, rótulo e placeholder da busca do modo completo
     $buscaCompacta  true mostra o atalho "Qualquer lugar" no modo compacto
     $mensagemNoTopo false quando a própria página exibe as mensagens da sessão
     $base           prefixo dos caminhos ('../' nas páginas do painel em admin/)
   ========================================================================= */

$base = $base ?? '';
$cabecalho = $cabecalho ?? 'completo';
$aba = $aba ?? '';
$busca = $busca ?? ['acao' => 'acomodacoes.php', 'rotulo' => 'Buscar acomodações'];
$buscaCompacta = $buscaCompacta ?? false;
$usuario = usuarioLogado();

/* Abas de categoria: chave => [link, ícone, texto] */
$abas = [
    'inicio' => ['index.php', 'bi-grid', 'Tudo'],
    'acomodacoes' => ['acomodacoes.php', 'bi-house-door', 'Acomodações'],
    'experiencias' => ['experiencias.php', 'bi-balloon', 'Experiências'],
    'servicos' => ['servicos.php', 'bi-bell', 'Serviços'],
];

/* Menu do painel administrativo (links relativos à pasta admin/) */
$abasAdmin = [
    'painel' => ['index.php', 'bi-speedometer2', 'Painel'],
    'acomodacoes' => ['acomodacoes.php', 'bi-house-door', 'Acomodações'],
    'reservas' => ['reservas.php', 'bi-calendar-check', 'Reservas'],
    'usuarios' => ['usuarios.php', 'bi-people', 'Usuários'],
];
$admin = adminLogado();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($descricao ?? 'Clone educacional do Airbnb.') ?>">
    <title><?= e($titulo ?? 'Clone Airbnb') ?></title>
    <link rel="icon" href="<?= $base ?>img/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= $base ?>css/style.css">
</head>
<body data-pagina="<?= e($pagina ?? '') ?>">
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <!-- ===== CABEÇALHO ===== -->
<?php if ($cabecalho === 'admin'): ?>
    <header class="site-header">
        <div class="container">
            <div class="header-topo">
                <a href="<?= $base ?>index.php" class="logo" aria-label="Clone Airbnb — ver o site">
                    <img src="<?= $base ?>img/logos/logo-airbnb.png" alt="" width="100" height="32">
                </a>
<?php if ($admin): ?>
                <nav class="abas" aria-label="Painel administrativo">
<?php foreach ($abasAdmin as $chave => [$link, $icone, $texto]): ?>
                    <a href="<?= $link ?>" class="aba"<?= $chave === $aba ? ' aria-current="page"' : '' ?>><i class="bi <?= $icone ?>" aria-hidden="true"></i><span><?= $texto ?></span></a>
<?php endforeach; ?>
                </nav>
<?php endif; ?>
                <div class="header-acoes">
                    <button type="button" class="icon-btn" data-acao="tema" aria-pressed="false" aria-label="Alternar tema escuro"><i class="bi bi-moon" aria-hidden="true"></i></button>
<?php if ($admin): ?>
                    <span class="admin-nome texto-suave"><i class="bi bi-person-badge" aria-hidden="true"></i> <?= e($admin['nome']) ?></span>
                    <a href="sair.php" class="btn btn-secundario btn-pequeno">Sair</a>
<?php endif; ?>
                </div>
            </div>
        </div>
    </header>
<?php elseif ($cabecalho === 'anfitriao'): ?>
    <header>
        <div class="container anfitriao-topo">
            <a href="<?= $base ?>index.php" class="logo" aria-label="Clone Airbnb — página inicial">
                <img src="<?= $base ?>img/logos/logo-airbnb.png" alt="" width="100" height="32">
            </a>
            <nav aria-label="Ações do anfitrião" style="display:flex;gap:8px">
                <button type="button" class="icon-btn" data-acao="tema" aria-pressed="false" aria-label="Alternar tema escuro"><i class="bi bi-moon" aria-hidden="true"></i></button>
                <a href="<?= $base ?>sair.php" class="btn btn-secundario btn-pequeno">Sair</a>
            </nav>
        </div>
    </header>
<?php else: ?>
    <header class="site-header">
        <div class="container">
            <div class="header-topo">
                <a href="<?= $base ?>index.php" class="logo" aria-label="Clone Airbnb — página inicial">
                    <img src="<?= $base ?>img/logos/logo-airbnb.png" alt="" width="100" height="32">
                </a>
<?php if ($cabecalho === 'completo'): ?>

                <button type="button" class="busca-abrir" aria-expanded="false" aria-controls="form-busca">
                    <i class="bi bi-search" aria-hidden="true"></i> Comece sua busca
                </button>

                <nav class="abas" aria-label="Categorias">
<?php foreach ($abas as $chave => [$link, $icone, $texto]): ?>
                    <a href="<?= $base . $link ?>" class="aba"<?= $chave === $aba ? ' aria-current="page"' : '' ?>><i class="bi <?= $icone ?>" aria-hidden="true"></i><span><?= $texto ?></span></a>
<?php endforeach; ?>
                </nav>
<?php elseif ($buscaCompacta): ?>
                <a href="<?= $base ?>acomodacoes.php" class="busca-compacta" aria-label="Fazer uma nova busca">
                    <span>Qualquer lugar</span><span class="sep opcional"></span><span class="opcional">Qualquer semana</span><span class="sep opcional"></span><span class="texto-suave opcional">Hóspedes?</span>
                    <span class="lupa"><i class="bi bi-search" aria-hidden="true"></i></span>
                </a>
<?php endif; ?>
<?php if ($cabecalho !== 'simples'): ?>

                <div class="header-acoes">
                    <a href="<?= $base ?>anfitriao.php" class="link-anfitriao">Torne-se um anfitrião</a>
                    <button type="button" class="icon-btn" data-acao="tema" aria-pressed="false" aria-label="Alternar tema escuro"><i class="bi bi-moon" aria-hidden="true"></i></button>
                    <div class="menu-usuario">
                        <button type="button" class="menu-toggle" aria-expanded="false" aria-controls="menu-usuario" aria-label="Abrir menu">
                            <i class="bi bi-list" aria-hidden="true"></i>
<?php if ($usuario): ?>
                            <span class="avatar-letra" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($usuario['nome'], 0, 1))) ?></span>
<?php else: ?>
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
<?php endif; ?>
                        </button>
                        <nav class="menu-dropdown" id="menu-usuario" aria-label="Menu do usuário" hidden>
<?php if ($usuario): ?>
                            <span class="menu-saudacao"><strong>Olá, <?= e(explode(' ', $usuario['nome'])[0]) ?></strong></span>
                            <a href="<?= $base ?>sair.php">Sair</a>
<?php else: ?>
                            <a href="<?= $base ?>login.php"><strong>Entrar</strong></a>
                            <a href="<?= $base ?>login.php#cadastro">Cadastrar-se</a>
<?php endif; ?>
                            <div class="separador" role="separator"></div>
                            <a href="<?= $base ?>favoritos.php">Favoritos <span class="contador contador-favoritos"></span></a>
                            <a href="<?= $base ?>anfitriao.php">Torne-se um anfitrião</a>
                            <a href="#rodape">Central de Ajuda</a>
                        </nav>
                    </div>
                </div>
<?php endif; ?>
            </div>
<?php if ($cabecalho === 'completo'): ?>

            <!-- Formulário de busca (GET): os parâmetros ficam na URL da listagem -->
            <form class="busca" id="form-busca" action="<?= e($busca['acao']) ?>" method="get" role="search" aria-label="<?= e($busca['rotulo']) ?>">
                <div class="busca-campo">
                    <label for="busca-onde">Onde</label>
                    <input type="search" id="busca-onde" name="onde" placeholder="<?= e($busca['placeholder'] ?? 'Buscar destinos') ?>" autocomplete="off" maxlength="80">
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
<?php endif; ?>
        </div>
    </header>
<?php endif; ?>
<?php if (($mensagemNoTopo ?? true) && ($mensagem = exibirMensagem())): ?>
    <div class="container mensagem-topo"><?= $mensagem ?></div>
<?php endif; ?>
