<?php
/* =========================================================================
   protege.php — Incluído no topo de todas as páginas do painel (admin/).
   Sem administrador na sessão, redireciona para o login com header().
   ========================================================================= */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcoes.php';
require_once __DIR__ . '/dados.php';

if (!adminLogado()) {
    definirMensagem('erro', 'Faça login para acessar o painel administrativo.');
    redirecionar('login.php');
}
