<?php
/* =========================================================================
   admin/acomodacoes.php — Lista de acomodações do painel (área restrita)
   A busca usa GET (o filtro fica na URL); a exclusão usa POST.
   ========================================================================= */
require_once '../includes/protege.php';

$busca = mb_substr(textoGet('busca'), 0, 80);
$lista = listarAcomodacoesPainel($busca);

$base = '../';
$titulo = 'Acomodações | Painel | Clone Airbnb';
$descricao = 'Gerenciar as acomodações do clone.';
$pagina = 'admin';
$cabecalho = 'admin';
$aba = 'acomodacoes';
require '../includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <div class="painel-topo">
            <h1 class="titulo-pagina">Acomodações</h1>
            <a href="acomodacao.php" class="btn btn-primario"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nova acomodação</a>
        </div>

        <!-- Busca por título ou cidade (GET) -->
        <form class="painel-busca" action="acomodacoes.php" method="get" role="search" aria-label="Buscar acomodações no painel">
            <div class="campo">
                <label for="painel-busca">Buscar por título ou cidade</label>
                <input type="search" id="painel-busca" name="busca" maxlength="80" value="<?= e($busca) ?>" placeholder="Ex.: Gramado">
            </div>
            <button type="submit" class="btn btn-escuro"><i class="bi bi-search" aria-hidden="true"></i> Buscar</button>
<?php if ($busca !== ''): ?>
            <a href="acomodacoes.php" class="btn btn-secundario">Limpar</a>
<?php endif; ?>
        </form>

        <p class="texto-suave" aria-live="polite"><strong><?= count($lista) ?></strong> acomodação(ões)<?= $busca !== '' ? ' para “' . e($busca) . '”' : '' ?></p>

<?php if (!$lista): ?>
        <div class="vazio"><i class="bi bi-house" aria-hidden="true"></i>Nenhuma acomodação encontrada.</div>
<?php else: ?>
        <div class="tabela-wrapper">
            <table class="tabela tabela-painel">
                <caption>Acomodações cadastradas, exibidas na listagem pública do site.</caption>
                <thead>
                    <tr>
                        <th scope="col">Foto</th>
                        <th scope="col">Título</th>
                        <th scope="col">Cidade</th>
                        <th scope="col" class="num">Diária</th>
                        <th scope="col" class="num">Hóspedes</th>
                        <th scope="col" class="num">Reservas</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($lista as $a): ?>
<?php $foto = paraLista($a['imagens'])[0] ?? 'casa-01'; ?>
                    <tr>
                        <td><img src="../img/acomodacoes/<?= e($foto) ?>.jpg" alt="" width="64" height="48" loading="lazy"></td>
                        <th scope="row"><?= e($a['titulo']) ?><span class="texto-suave"><?= e($a['tipo']) ?> · mín. <?= (int) $a['min_noites'] ?> noite(s)</span></th>
                        <td><?= e($a['cidade']) ?> - <?= e($a['estado']) ?></td>
                        <td class="num"><?= formatarMoeda((float) $a['diaria']) ?></td>
                        <td class="num"><?= (int) $a['capacidade'] ?></td>
                        <td class="num"><?= (int) $a['reservas'] ?></td>
                        <td>
                            <div class="acoes-tabela">
                                <a href="../anuncio.php?id=<?= (int) $a['id'] ?>" class="icon-btn" aria-label="Ver anúncio de <?= e($a['titulo']) ?> no site"><i class="bi bi-eye" aria-hidden="true"></i></a>
                                <a href="acomodacao.php?id=<?= (int) $a['id'] ?>" class="icon-btn" aria-label="Editar <?= e($a['titulo']) ?>"><i class="bi bi-pencil" aria-hidden="true"></i></a>
                                <form action="excluir-acomodacao.php" method="post" class="form-excluir" data-titulo="<?= e($a['titulo']) ?>">
                                    <?= campoCsrf() ?>
                                    <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                    <button type="submit" class="icon-btn btn-perigo" aria-label="Excluir <?= e($a['titulo']) ?>"><i class="bi bi-trash" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php endif; ?>
    </main>

    <!-- Confirmação de exclusão (aberta pelo js/admin.js) -->
    <dialog class="modal" id="modal-excluir" aria-labelledby="titulo-excluir">
        <div class="modal-cabecalho">
            <h2 id="titulo-excluir">Excluir acomodação</h2>
            <button type="button" class="icon-btn btn-fechar" data-fechar aria-label="Fechar"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
        </div>
        <div class="modal-corpo">
            <p>Tem certeza que deseja excluir <strong id="excluir-nome"></strong>? Essa ação não pode ser desfeita.</p>
        </div>
        <div class="modal-rodape painel-modal-acoes">
            <button type="button" class="btn btn-secundario" data-fechar>Cancelar</button>
            <button type="button" class="btn btn-primario" id="confirmar-excluir">Excluir</button>
        </div>
    </dialog>

<?php
$scripts = ['validacao', 'admin'];
require '../includes/rodape.php';
