<?php
/* =========================================================================
   admin/acomodacao.php — Cadastrar (sem id) ou editar (?id=N) acomodação
   ========================================================================= */
require_once '../includes/protege.php';

/* Lê uma lista de valores enviada por POST (checkboxes e selects com []) */
function listaPost(string $campo): array
{
    $valores = filter_input(INPUT_POST, $campo, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    return is_array($valores) ? array_values(array_map('trim', array_filter($valores, 'is_string'))) : [];
}

$id = inteiro(INPUT_GET, 'id', 1);
$editando = $id !== null;

if ($editando) {
    $valores = buscarAcomodacao($id);
    if (!$valores) {
        definirMensagem('erro', 'Acomodação não encontrada.');
        redirecionar('acomodacoes.php');
    }
} else {
    // valores iniciais do formulário de cadastro
    $valores = [
        'titulo' => '', 'tipo' => '', 'cidade' => '', 'estado' => '', 'descricao' => '',
        'diaria' => '', 'capacidade' => 2, 'quartos' => 1, 'camas' => 1, 'banheiros' => 1, 'minNoites' => 1,
        'nota' => 0, 'avaliacoes' => 0, 'selo' => '', 'anfitriao' => '', 'anfitriaoDesde' => date('Y'),
        'categorias' => [], 'imagens' => [], 'comodidades' => [],
    ];
}
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido()) {
        definirMensagem('erro', 'Sua sessão expirou. Envie o formulário de novo.');
        redirecionar('acomodacao.php' . ($editando ? "?id={$id}" : ''));
    }

    $valores = [
        'titulo' => preg_replace('/\s+/', ' ', textoPost('titulo')),
        'tipo' => textoPost('tipo'),
        'cidade' => textoPost('cidade'),
        'estado' => textoPost('estado'),
        'descricao' => textoPost('descricao'),
        'diaria' => str_replace(',', '.', textoPost('diaria')),
        'capacidade' => textoPost('capacidade'),
        'quartos' => textoPost('quartos'),
        'camas' => textoPost('camas'),
        'banheiros' => textoPost('banheiros'),
        'minNoites' => textoPost('minNoites'),
        'nota' => str_replace(',', '.', textoPost('nota')),
        'avaliacoes' => textoPost('avaliacoes'),
        'selo' => textoPost('selo'),
        'anfitriao' => textoPost('anfitriao'),
        'anfitriaoDesde' => textoPost('anfitriaoDesde'),
        'categorias' => listaPost('categorias'),
        'imagens' => listaPost('imagens'),
        'comodidades' => listaPost('comodidades'),
    ];

    $erros = validarAcomodacao($valores);
    if (!$erros) {
        $salvoId = salvarAcomodacao($valores, $editando ? $id : null);
        definirMensagem('sucesso', $editando
            ? "Acomodação “{$valores['titulo']}” atualizada."
            : "Acomodação “{$valores['titulo']}” cadastrada (#{$salvoId}). Ela já aparece no site.");
        redirecionar('acomodacoes.php');
    }
}

$fotos = fotosDisponiveis();
$acao = 'acomodacao.php' . ($editando ? "?id={$id}" : '');

$base = '../';
$titulo = ($editando ? 'Editar' : 'Nova') . ' acomodação | Painel | Clone Airbnb';
$descricao = 'Cadastro de acomodações do clone.';
$pagina = 'admin';
$cabecalho = 'admin';
$aba = 'acomodacoes';
require '../includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container conteudo">
        <div class="checkout-titulo">
            <a href="acomodacoes.php" class="icon-btn" aria-label="Voltar para a lista"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
            <h1 class="titulo-pagina"><?= $editando ? 'Editar acomodação' : 'Nova acomodação' ?></h1>
        </div>

        <form id="form-acomodacao" class="form-pilha painel-form" action="<?= e($acao) ?>" method="post">
            <?= campoCsrf() ?>
            <p class="alerta alerta-erro" data-resumo role="alert"<?= $erros ? '' : ' hidden' ?>><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span><?= $erros ? 'Corrija os campos destacados para salvar.' : '' ?></span></p>

            <!-- Informações principais -->
            <section class="etapa" aria-labelledby="secao-info">
                <h2 id="secao-info">Informações do anúncio</h2>
                <div class="campo">
                    <label for="ac-titulo">Título</label>
                    <input type="text" id="ac-titulo" name="titulo" required minlength="3" maxlength="120" data-nome="O título" value="<?= e($valores['titulo']) ?>"<?= invalido($erros, 'titulo', 'ac-titulo') ?>>
                    <?= erroCampo($erros, 'titulo', 'ac-titulo') ?>
                </div>
                <div class="campo-linha">
                    <div class="campo">
                        <label for="ac-tipo">Tipo</label>
                        <select id="ac-tipo" name="tipo" required data-nome="O tipo"<?= invalido($erros, 'tipo', 'ac-tipo') ?>>
                            <option value="">Selecione</option>
<?php foreach (TIPOS_ACOMODACAO as $tipo): ?>
                            <option<?= $tipo === $valores['tipo'] ? ' selected' : '' ?>><?= e($tipo) ?></option>
<?php endforeach; ?>
                        </select>
                        <?= erroCampo($erros, 'tipo', 'ac-tipo') ?>
                    </div>
                    <div class="campo">
                        <label for="ac-selo">Selo</label>
                        <select id="ac-selo" name="selo"<?= invalido($erros, 'selo', 'ac-selo') ?>>
                            <option value="">Nenhum</option>
<?php foreach (SELOS as $selo): ?>
                            <option<?= $selo === $valores['selo'] ? ' selected' : '' ?>><?= e($selo) ?></option>
<?php endforeach; ?>
                        </select>
                        <?= erroCampo($erros, 'selo', 'ac-selo') ?>
                    </div>
                </div>
                <div class="campo-linha">
                    <div class="campo">
                        <label for="ac-cidade">Cidade</label>
                        <input type="text" id="ac-cidade" name="cidade" required minlength="2" maxlength="80" data-nome="A cidade" value="<?= e($valores['cidade']) ?>"<?= invalido($erros, 'cidade', 'ac-cidade') ?>>
                        <?= erroCampo($erros, 'cidade', 'ac-cidade') ?>
                    </div>
                    <div class="campo">
                        <label for="ac-estado">Estado</label>
                        <input type="text" id="ac-estado" name="estado" required minlength="2" maxlength="40" data-nome="O estado" placeholder="Ex.: Santa Catarina" value="<?= e($valores['estado']) ?>"<?= invalido($erros, 'estado', 'ac-estado') ?>>
                        <?= erroCampo($erros, 'estado', 'ac-estado') ?>
                    </div>
                </div>
                <div class="campo">
                    <label for="ac-descricao">Descrição</label>
                    <textarea id="ac-descricao" name="descricao" rows="4" required minlength="10" maxlength="2000" data-nome="A descrição"<?= invalido($erros, 'descricao', 'ac-descricao') ?>><?= e($valores['descricao']) ?></textarea>
                    <?= erroCampo($erros, 'descricao', 'ac-descricao') ?>
                </div>
            </section>

            <!-- Números do anúncio (RN1 usa o mínimo de noites; RN2 usa a capacidade) -->
            <section class="etapa" aria-labelledby="secao-numeros">
                <h2 id="secao-numeros">Preço e capacidade</h2>
                <div class="painel-grade-campos">
<?php
$camposNumero = [
    'diaria' => ['Diária (R$)', 50, 10000, '0.01'],
    'capacidade' => ['Máximo de hóspedes', 1, 16, '1'],
    'minNoites' => ['Mínimo de noites', 1, 30, '1'],
    'quartos' => ['Quartos', 1, 20, '1'],
    'camas' => ['Camas', 1, 30, '1'],
    'banheiros' => ['Banheiros', 1, 20, '1'],
];
foreach ($camposNumero as $campo => [$rotulo, $min, $max, $passo]): ?>
                    <div class="campo">
                        <label for="ac-<?= $campo ?>"><?= $rotulo ?></label>
                        <input type="number" id="ac-<?= $campo ?>" name="<?= $campo ?>" required min="<?= $min ?>" max="<?= $max ?>" step="<?= $passo ?>" data-nome="<?= e($rotulo) ?>" value="<?= e($valores[$campo]) ?>"<?= invalido($erros, $campo, "ac-{$campo}") ?>>
                        <?= erroCampo($erros, $campo, "ac-{$campo}") ?>
                    </div>
<?php endforeach; ?>
                </div>
            </section>

            <!-- Anfitrião e avaliações (dados fictícios) -->
            <section class="etapa" aria-labelledby="secao-anfitriao">
                <h2 id="secao-anfitriao">Anfitrião e avaliações</h2>
                <div class="painel-grade-campos">
                    <div class="campo">
                        <label for="ac-anfitriao">Nome do anfitrião</label>
                        <input type="text" id="ac-anfitriao" name="anfitriao" required minlength="2" maxlength="80" data-nome="O nome do anfitrião" value="<?= e($valores['anfitriao']) ?>"<?= invalido($erros, 'anfitriao', 'ac-anfitriao') ?>>
                        <?= erroCampo($erros, 'anfitriao', 'ac-anfitriao') ?>
                    </div>
                    <div class="campo">
                        <label for="ac-anfitriaoDesde">Anfitrião desde (ano)</label>
                        <input type="number" id="ac-anfitriaoDesde" name="anfitriaoDesde" required min="2008" max="<?= date('Y') ?>" data-nome="O ano" value="<?= e($valores['anfitriaoDesde']) ?>"<?= invalido($erros, 'anfitriaoDesde', 'ac-anfitriaoDesde') ?>>
                        <?= erroCampo($erros, 'anfitriaoDesde', 'ac-anfitriaoDesde') ?>
                    </div>
                    <div class="campo">
                        <label for="ac-nota">Nota (0 a 5)</label>
                        <input type="number" id="ac-nota" name="nota" required min="0" max="5" step="0.01" data-nome="A nota" value="<?= e($valores['nota']) ?>"<?= invalido($erros, 'nota', 'ac-nota') ?>>
                        <?= erroCampo($erros, 'nota', 'ac-nota') ?>
                    </div>
                    <div class="campo">
                        <label for="ac-avaliacoes">Nº de avaliações</label>
                        <input type="number" id="ac-avaliacoes" name="avaliacoes" required min="0" max="100000" data-nome="O número de avaliações" value="<?= e($valores['avaliacoes']) ?>"<?= invalido($erros, 'avaliacoes', 'ac-avaliacoes') ?>>
                        <?= erroCampo($erros, 'avaliacoes', 'ac-avaliacoes') ?>
                    </div>
                </div>
            </section>

            <!-- Categorias e comodidades -->
            <section class="etapa" aria-labelledby="secao-opcoes">
                <h2 id="secao-opcoes">Categorias e comodidades</h2>
                <fieldset class="campo painel-opcoes" id="ac-categorias">
                    <legend class="rotulo">Categorias (filtros da listagem)</legend>
                    <div class="opcoes-check">
<?php foreach (CATEGORIAS as $chave => $nome): ?>
                        <label><input type="checkbox" name="categorias[]" value="<?= $chave ?>"<?= in_array($chave, $valores['categorias'], true) ? ' checked' : '' ?>> <?= $nome ?></label>
<?php endforeach; ?>
                    </div>
                    <?= erroCampo($erros, 'categorias', 'ac-categorias') ?>
                </fieldset>
                <fieldset class="campo painel-opcoes" id="ac-comodidades">
                    <legend class="rotulo">Comodidades</legend>
                    <div class="opcoes-check">
<?php foreach (COMODIDADES as $comodidade): ?>
                        <label><input type="checkbox" name="comodidades[]" value="<?= e($comodidade) ?>"<?= in_array($comodidade, $valores['comodidades'], true) ? ' checked' : '' ?>> <?= e($comodidade) ?></label>
<?php endforeach; ?>
                    </div>
                    <?= erroCampo($erros, 'comodidades', 'ac-comodidades') ?>
                </fieldset>
            </section>

            <!-- Fotos: escolhidas entre as imagens de img/acomodacoes -->
            <section class="etapa" aria-labelledby="secao-fotos">
                <h2 id="secao-fotos">Fotos</h2>
                <p class="texto-suave" style="font-size:.875rem">Escolha <?= TOTAL_FOTOS ?> fotos diferentes. A primeira é a capa exibida nos cards.</p>
                <div class="fotos-escolha" id="ac-imagens">
<?php for ($i = 0; $i < TOTAL_FOTOS; $i++): $atual = $valores['imagens'][$i] ?? ''; ?>
                    <div class="campo foto-opcao">
                        <img src="<?= $atual ? '../img/acomodacoes/' . e($atual) . '.jpg' : '../img/favicon.svg' ?>" alt="" data-previa="ac-foto-<?= $i ?>">
                        <label for="ac-foto-<?= $i ?>"><?= $i === 0 ? 'Capa' : 'Foto ' . ($i + 1) ?></label>
                        <select id="ac-foto-<?= $i ?>" name="imagens[]" required data-nome="A foto">
                            <option value="">Selecione</option>
<?php foreach ($fotos as $foto): ?>
                            <option<?= $foto === $atual ? ' selected' : '' ?>><?= e($foto) ?></option>
<?php endforeach; ?>
                        </select>
                    </div>
<?php endfor; ?>
                </div>
                <?= erroCampo($erros, 'imagens', 'ac-imagens') ?>
            </section>

            <div class="painel-modal-acoes">
                <a href="acomodacoes.php" class="btn btn-secundario">Cancelar</a>
                <button type="submit" class="btn btn-primario"><?= $editando ? 'Salvar alterações' : 'Cadastrar acomodação' ?></button>
            </div>
        </form>
    </main>

<?php
$scripts = ['validacao', 'admin'];
require '../includes/rodape.php';
