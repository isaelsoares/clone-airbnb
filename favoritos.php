<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';
require_once 'includes/dados.php';

$acomodacoes = listarAcomodacoes();

$titulo = 'Favoritos | Clone Airbnb';
$descricao = 'Acomodações, experiências e serviços salvos nos favoritos.';
$pagina = 'favoritos';
$cabecalho = 'completo';
$aba = '';
$busca = ['acao' => 'acomodacoes.php', 'rotulo' => 'Buscar acomodações'];
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <div class="resultado-resumo">
            <h1 class="titulo-pagina">Favoritos</h1>
            <button type="button" class="btn btn-secundario btn-pequeno" id="limpar-favoritos">Limpar todos</button>
        </div>
        <p id="resultado-texto" aria-live="polite" class="texto-suave"></p>
        <div id="lista-favoritos" class="grade"></div>
    </main>

<?php
$inspiracao = true;
$modalDetalhe = true;
$scripts = ['catalogo'];
require 'includes/rodape.php';
