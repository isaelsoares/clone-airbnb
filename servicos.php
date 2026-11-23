<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$titulo = 'Serviços | Clone Airbnb';
$descricao = 'Serviços fictícios para sua viagem: fotografia, chefs, massagens e mais.';
$pagina = 'servicos';
$cabecalho = 'completo';
$aba = 'servicos';
$busca = ['acao' => 'servicos.php', 'rotulo' => 'Buscar serviços'];
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <h1 class="visually-hidden">Serviços</h1>

        <section aria-labelledby="titulo-categorias">
            <h2 id="titulo-categorias" style="margin-bottom:16px">Serviços por categoria</h2>
            <div class="tiles" id="tiles-servicos"></div>
        </section>

        <p id="resultado-texto" aria-live="polite" class="texto-suave"></p>
        <div id="secoes-dinamicas" class="conteudo" style="padding:0"></div>

        <!-- Tabela de preços (gerada a partir do array SERVICOS) -->
        <section aria-labelledby="titulo-tabela">
            <h2 id="titulo-tabela" style="margin-bottom:16px">Compare preços dos serviços</h2>
            <div class="tabela-wrapper">
                <table class="tabela">
                    <caption>Valores fictícios, a partir de, por grupo.</caption>
                    <thead>
                        <tr>
                            <th scope="col">Serviço</th>
                            <th scope="col">Categoria</th>
                            <th scope="col">Cidade</th>
                            <th scope="col" class="num">Avaliação</th>
                            <th scope="col" class="num">Preço</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-servicos"></tbody>
                </table>
            </div>
        </section>
    </main>

<?php
$inspiracao = true;
$modalDetalhe = true;
$scripts = ['catalogo'];
require 'includes/rodape.php';
