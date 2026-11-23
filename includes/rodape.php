<?php
/* =========================================================================
   rodape.php — Rodapé do site, scripts e fechamento do <body>
   Variáveis que a página define antes do include:
     $inspiracao    true mostra "Inspiração para viagens futuras"
     $modalDetalhe  true inclui o modal de experiências e serviços
     $scripts       arquivos de js/ carregados depois de dados.js e main.js
     $acomodacoes   lista vinda do banco, entregue ao JS como ACOMODACOES
   ========================================================================= */

$inspiracao = $inspiracao ?? false;
$modalDetalhe = $modalDetalhe ?? false;
$scripts = $scripts ?? [];
?>
<?php if ($inspiracao): ?>

    <!-- ===== INSPIRAÇÃO ===== -->
    <section class="inspiracao" aria-labelledby="titulo-inspiracao">
        <div class="container">
            <h2 id="titulo-inspiracao">Inspiração para viagens futuras</h2>
            <div class="inspiracao-abas" role="tablist" aria-label="Tipos de destino"></div>
            <ul class="inspiracao-lista" id="painel-inspiracao" role="tabpanel"></ul>
        </div>
    </section>
<?php endif; ?>

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
                <p>© <?= date('Y') ?> Clone Airbnb · Privacidade · Termos</p>
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
<?php if ($modalDetalhe): ?>

    <!-- Modal de detalhes (experiências e serviços) -->
    <dialog class="modal" id="modal-detalhe" aria-labelledby="modal-detalhe-titulo"></dialog>
<?php endif; ?>

<?php if (isset($acomodacoes)): ?>
    <!-- Acomodações cadastradas no painel (lidas do MySQL pelo PHP) -->
    <script>const ACOMODACOES = <?= acomodacoesParaJs($acomodacoes) ?>;</script>
<?php endif; ?>
    <script src="js/dados.js"></script>
    <script src="js/main.js"></script>
<?php foreach ($scripts as $script): ?>
    <script src="js/<?= e($script) ?>.js"></script>
<?php endforeach; ?>
</body>
</html>
