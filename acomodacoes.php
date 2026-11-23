<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$titulo = 'Acomodações | Clone Airbnb';
$descricao = 'Busque e filtre acomodações por destino, categoria e preço.';
$pagina = 'acomodacoes';
$cabecalho = 'completo';
$aba = 'acomodacoes';
$busca = ['acao' => 'acomodacoes.php', 'rotulo' => 'Buscar acomodações'];
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <h1 class="titulo-pagina">Acomodações</h1>

        <!-- Filtros do catálogo (tratados pelo js/catalogo.js) -->
        <form class="filtros" id="form-filtros" aria-label="Filtrar acomodações">
            <fieldset>
                <legend>Categoria</legend>
                <div class="chips" id="filtro-categorias">
                    <button type="button" class="chip" data-categoria="" aria-pressed="true"><i class="bi bi-grid" aria-hidden="true"></i> Todas</button>
                    <button type="button" class="chip" data-categoria="praia" aria-pressed="false"><i class="bi bi-umbrella" aria-hidden="true"></i> Praia</button>
                    <button type="button" class="chip" data-categoria="piscina" aria-pressed="false"><i class="bi bi-water" aria-hidden="true"></i> Piscina</button>
                    <button type="button" class="chip" data-categoria="chale" aria-pressed="false"><i class="bi bi-tree" aria-hidden="true"></i> Chalés</button>
                    <button type="button" class="chip" data-categoria="hotel" aria-pressed="false"><i class="bi bi-building" aria-hidden="true"></i> Hotéis</button>
                    <button type="button" class="chip" data-categoria="urbano" aria-pressed="false"><i class="bi bi-buildings" aria-hidden="true"></i> Urbano</button>
                </div>
            </fieldset>
            <div class="filtro-preco">
                <label for="filtro-preco">Preço máximo por noite: <output id="saida-preco" for="filtro-preco">R$ 1.500,00</output></label>
                <input type="range" id="filtro-preco" name="preco" min="300" max="1500" step="50" value="1500">
            </div>
            <div class="filtro-ordem">
                <label for="filtro-ordem">Ordenar por</label><br>
                <select id="filtro-ordem" name="ordem">
                    <option value="relevancia">Relevância</option>
                    <option value="menor-preco">Menor preço</option>
                    <option value="maior-preco">Maior preço</option>
                    <option value="nota">Melhor avaliação</option>
                </select>
            </div>
            <button type="reset" class="btn btn-secundario btn-pequeno">Limpar filtros</button>
        </form>

        <div class="resultado-resumo">
            <p id="resultado-texto" aria-live="polite"></p>
        </div>

        <!-- Sem filtros: carrosséis por cidade. Com busca/filtro: grade de resultados -->
        <div id="resultados" class="conteudo" style="padding:0"></div>
    </main>

<?php
$inspiracao = true;
$modalDetalhe = true;
$scripts = ['catalogo'];
require 'includes/rodape.php';
