<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$titulo = 'Experiências | Clone Airbnb';
$descricao = 'Experiências fictícias oferecidas por anfitriões locais.';
$pagina = 'experiencias';
$cabecalho = 'completo';
$aba = 'experiencias';
$busca = ['acao' => 'experiencias.php', 'rotulo' => 'Buscar experiências', 'placeholder' => 'Cidade ou ponto de referência'];
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <h1 class="visually-hidden">Experiências</h1>
        <p id="resultado-texto" aria-live="polite" class="texto-suave"></p>
        <div id="secoes-dinamicas" class="conteudo" style="padding:0"></div>
    </main>

<?php
$inspiracao = true;
$modalDetalhe = true;
$scripts = ['catalogo'];
require 'includes/rodape.php';
