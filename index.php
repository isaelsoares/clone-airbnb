<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';
require_once 'includes/dados.php';

$acomodacoes = listarAcomodacoes();

$titulo = 'Clone Airbnb | Casas de temporada, cabanas, casas de praia e muito mais';
$descricao = 'Clone educacional do Airbnb: encontre acomodações, experiências e serviços.';
$pagina = 'inicio';
$cabecalho = 'completo';
$aba = 'inicio';
$busca = ['acao' => 'acomodacoes.php', 'rotulo' => 'Buscar acomodações'];
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
      <h1 class="visually-hidden">
        Encontre acomodações, experiências e serviços
      </h1>

      <!-- Carrosséis gerados pelo js/catalogo.js a partir de js/dados.js -->
      <div id="secoes-dinamicas" class="conteudo" style="padding: 0"></div>

      <section aria-labelledby="titulo-servicos">
        <h2 id="titulo-servicos" style="margin-bottom: 16px">
          Encontre serviços perto de você
        </h2>
        <div class="tiles" id="tiles-servicos"></div>
      </section>

      <section aria-labelledby="titulo-destinos">
        <h2 id="titulo-destinos" style="margin-bottom: 16px">
          Destinos para você
        </h2>
        <div class="destinos" id="lista-destinos"></div>
      </section>

      <div id="secao-experiencias"></div>
    </main>

<?php
$inspiracao = true;
$modalDetalhe = true;
$scripts = ['catalogo'];
require 'includes/rodape.php';
