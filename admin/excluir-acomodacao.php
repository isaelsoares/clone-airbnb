<?php
/* =========================================================================
   admin/excluir-acomodacao.php — Exclui uma acomodação (somente POST)
   ========================================================================= */
require_once '../includes/protege.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValido()) {
    definirMensagem('erro', 'Requisição inválida. Tente novamente pela lista.');
    redirecionar('acomodacoes.php');
}

$id = inteiro(INPUT_POST, 'id', 1);
$acomodacao = $id ? buscarAcomodacao($id) : null;

if (!$acomodacao) {
    definirMensagem('erro', 'Acomodação não encontrada.');
} elseif (($total = contarReservasDaAcomodacao($id)) > 0) {
    // as reservas seriam apagadas junto (ON DELETE CASCADE), então a exclusão é bloqueada
    definirMensagem('erro', "Não é possível excluir “{$acomodacao['titulo']}”: ela tem {$total} reserva(s).");
} elseif (excluirAcomodacao($id)) {
    definirMensagem('sucesso', "Acomodação “{$acomodacao['titulo']}” excluída.");
}

redirecionar('acomodacoes.php');
