<?php
/* =========================================================================
   admin/index.php — Página inicial do painel (área restrita)
   ========================================================================= */
require_once '../includes/protege.php';

$resumo = resumoPainel();

$base = '../';
$titulo = 'Painel | Clone Airbnb';
$descricao = 'Painel administrativo do clone.';
$pagina = 'admin';
$cabecalho = 'admin';
$aba = 'painel';
require '../includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <h1 class="titulo-pagina">Painel administrativo</h1>

        <section aria-labelledby="titulo-resumo">
            <h2 id="titulo-resumo" class="visually-hidden">Resumo</h2>
            <div class="painel-cards">
                <article class="painel-card">
                    <i class="bi bi-house-door" aria-hidden="true"></i>
                    <h3>Acomodações</h3>
                    <p class="painel-numero"><?= $resumo['acomodacoes'] ?></p>
                </article>
                <article class="painel-card">
                    <i class="bi bi-calendar-check" aria-hidden="true"></i>
                    <h3>Reservas</h3>
                    <p class="painel-numero"><?= $resumo['reservas'] ?></p>
                </article>
                <article class="painel-card">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    <h3>Usuários</h3>
                    <p class="painel-numero"><?= $resumo['usuarios'] ?></p>
                </article>
                <article class="painel-card">
                    <i class="bi bi-cash-coin" aria-hidden="true"></i>
                    <h3>Total reservado</h3>
                    <p class="painel-numero"><?= formatarMoeda($resumo['faturamento']) ?></p>
                </article>
            </div>
        </section>
    </main>

<?php
$scripts = ['validacao', 'admin'];
require '../includes/rodape.php';
