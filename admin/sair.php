<?php
/* =========================================================================
   admin/sair.php — Logout do administrador
   ========================================================================= */
require_once '../includes/config.php';
require_once '../includes/funcoes.php';

unset($_SESSION['admin']);
session_regenerate_id(true);

definirMensagem('info', 'Você saiu do painel.');
redirecionar('login.php');
