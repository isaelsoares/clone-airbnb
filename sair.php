<?php
/* =========================================================================
   sair.php — Logout do usuário: remove os dados da sessão e volta ao início
   ========================================================================= */
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

unset($_SESSION['usuario']);
session_regenerate_id(true);

definirMensagem('info', 'Você saiu da sua conta.');
redirecionar('index.php');
