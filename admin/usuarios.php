<?php
/* =========================================================================
   admin/usuarios.php — Usuários cadastrados pelo site (área restrita)
   ========================================================================= */
require_once '../includes/protege.php';

$usuarios = listarUsuarios();

$base = '../';
$titulo = 'Usuários | Painel | Clone Airbnb';
$descricao = 'Usuários cadastrados no clone.';
$pagina = 'admin';
$cabecalho = 'admin';
$aba = 'usuarios';
require '../includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <h1 class="titulo-pagina">Usuários</h1>
        <p class="texto-suave"><strong><?= count($usuarios) ?></strong> usuário(s) cadastrados pelo formulário do site.</p>

<?php if (!$usuarios): ?>
        <div class="vazio"><i class="bi bi-people" aria-hidden="true"></i>Nenhum usuário cadastrado ainda.</div>
<?php else: ?>
        <div class="tabela-wrapper">
            <table class="tabela tabela-painel">
                <caption>As senhas ficam no banco apenas como hash (password_hash) e não são exibidas.</caption>
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Celular</th>
                        <th scope="col" class="num">Idade</th>
                        <th scope="col">Cadastro</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($usuarios as $u): ?>
                    <tr>
                        <th scope="row"><?= e($u['nome']) ?></th>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['telefone']) ?></td>
                        <td class="num"><?= idade($u['nascimento']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($u['criado_em'])) ?></td>
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
