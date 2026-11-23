<?php
/* =========================================================================
   admin/login.php — Login do administrador do painel
   A senha do banco foi gravada com password_hash e é conferida com
   password_verify; o administrador fica guardado em $_SESSION['admin'].
   ========================================================================= */
require_once '../includes/config.php';
require_once '../includes/funcoes.php';
require_once '../includes/dados.php';

if (adminLogado()) {
    redirecionar('index.php');
}

$email = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(textoPost('email'));
    $senha = (string) ($_POST['senha'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erro = 'Informe um e-mail válido e a senha.';
    } else {
        $admin = buscarAdminPorEmail($email);
        if ($admin && password_verify($senha, $admin['senha'])) {
            session_regenerate_id(true); // evita fixação de sessão
            $_SESSION['admin'] = ['id' => (int) $admin['id'], 'nome' => $admin['nome'], 'email' => $admin['email']];
            definirMensagem('sucesso', "Olá, {$admin['nome']}! Login realizado.");
            redirecionar('index.php');
        }
        $erro = 'E-mail ou senha incorretos.';
    }
}

$base = '../';
$titulo = 'Login do painel | Clone Airbnb';
$descricao = 'Acesso restrito ao painel administrativo do clone.';
$pagina = 'admin-login';
$cabecalho = 'admin';
$mensagemNoTopo = false;
require '../includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="login-fundo">
        <section class="login-cartao" aria-labelledby="titulo-login">
            <h1 id="titulo-login"><i class="bi bi-shield-lock" aria-hidden="true"></i> Painel administrativo</h1>
            <?= exibirMensagem() ?>

            <form class="form-pilha" id="form-admin-login" action="login.php" method="post">
                <p class="alerta alerta-erro" data-resumo role="alert"<?= $erro ? '' : ' hidden' ?>><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span><?= e($erro) ?></span></p>
                <div class="campo">
                    <label for="admin-email">E-mail</label>
                    <input type="email" id="admin-email" name="email" required autocomplete="username" data-nome="O e-mail" value="<?= e($email) ?>">
                </div>
                <div class="campo">
                    <label for="admin-senha">Senha</label>
                    <input type="password" id="admin-senha" name="senha" required autocomplete="current-password" data-nome="A senha">
                </div>
                <button type="submit" class="btn btn-primario btn-bloco">Entrar no painel</button>
            </form>
            <a href="../index.php" class="link-sublinhado" style="font-size:.875rem;text-align:center">Voltar ao site</a>
        </section>
    </main>

<?php
$scripts = ['validacao', 'admin'];
require '../includes/rodape.php';
