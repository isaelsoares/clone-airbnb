<?php
/* =========================================================================
   admin/reservas.php — Reservas gravadas pelo checkout (área restrita)
   ========================================================================= */
require_once '../includes/protege.php';

$reservas = listarReservas();

$base = '../';
$titulo = 'Reservas | Painel | Clone Airbnb';
$descricao = 'Reservas feitas no checkout do clone.';
$pagina = 'admin';
$cabecalho = 'admin';
$aba = 'reservas';
require '../includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <h1 class="titulo-pagina">Reservas</h1>
        <p class="texto-suave"><strong><?= count($reservas) ?></strong> reserva(s) feitas pelo formulário de checkout.</p>

<?php if (!$reservas): ?>
        <div class="vazio"><i class="bi bi-calendar-x" aria-hidden="true"></i>Nenhuma reserva ainda.</div>
<?php else: ?>
        <div class="tabela-wrapper">
            <table class="tabela tabela-painel">
                <caption>O CPF aparece mascarado; dados de cartão não são gravados.</caption>
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Acomodação</th>
                        <th scope="col">Hóspede</th>
                        <th scope="col">Estadia</th>
                        <th scope="col" class="num">Hóspedes</th>
                        <th scope="col">Pagamento</th>
                        <th scope="col" class="num">Total</th>
                        <th scope="col">Feita em</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($reservas as $r): ?>
                    <tr>
                        <th scope="row"><?= codigoReserva((int) $r['id']) ?></th>
                        <td><a href="../anuncio.php?id=<?= (int) $r['acomodacao_id'] ?>"><?= e($r['titulo']) ?></a></td>
                        <td><?= e($r['nome']) ?><span class="texto-suave linha-extra"><?= e($r['email']) ?> · CPF <?= cpfMascarado($r['cpf']) ?></span></td>
                        <td><?= formatarData($r['checkin']) ?> – <?= formatarData($r['checkout']) ?><span class="texto-suave linha-extra"><?= contarNoites($r['checkin'], $r['checkout']) ?> noite(s)</span></td>
                        <td class="num"><?= (int) $r['hospedes'] ?></td>
                        <td><?= $r['forma_pagamento'] === 'cartao' ? 'Cartão' : 'Pix' ?></td>
                        <td class="num"><?= formatarMoeda((float) $r['total']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($r['criado_em'])) ?></td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php endif; ?>
    </main>

<?php
$scripts = ['admin'];
require '../includes/rodape.php';
