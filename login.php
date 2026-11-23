<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';
require_once 'includes/dados.php';

// quem já está logado não precisa ver esta página
if (usuarioLogado()) {
    redirecionar('index.php');
}

$abaAtiva = 'entrar';
$erros = [];
$erroLogin = '';
$emailLogin = '';
$valores = ['nome' => '', 'email' => '', 'telefone' => '', 'nascimento' => ''];

/* ---------- Login de usuário (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && textoPost('acao') === 'entrar') {
    $emailLogin = mb_strtolower(textoPost('email'));
    $senha = (string) ($_POST['senha'] ?? '');

    if (!filter_var($emailLogin, FILTER_VALIDATE_EMAIL) || $senha === '') {
        $erroLogin = 'Informe um e-mail válido e a sua senha.';
    } else {
        $usuario = buscarUsuarioPorEmail($emailLogin);
        // password_verify compara a senha digitada com o hash salvo no banco
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true); // novo id de sessão após o login
            $_SESSION['usuario'] = ['id' => (int) $usuario['id'], 'nome' => $usuario['nome']];
            $primeiroNome = explode(' ', $usuario['nome'])[0];
            definirMensagem('sucesso', "Boas-vindas, {$primeiroNome}!");
            redirecionar('index.php');
        }
        // mesma mensagem para e-mail ou senha errados (não revela quem tem conta)
        $erroLogin = 'E-mail ou senha incorretos.';
    }
}

/* ---------- Cadastro de usuário (POST) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && textoPost('acao') === 'cadastrar') {
    $abaAtiva = 'cadastro';
    $valores = [
        'nome' => preg_replace('/\s+/', ' ', textoPost('nome')),
        'email' => mb_strtolower(textoPost('email')),
        'telefone' => textoPost('telefone'),
        'nascimento' => textoPost('nascimento'),
    ];
    // a senha não passa por trim: vai direto para o password_hash
    $senha = (string) ($_POST['senha'] ?? '');
    $confirmar = (string) ($_POST['confirmar'] ?? '');
    $termos = filter_input(INPUT_POST, 'termos') !== null;

    // validação no servidor, independente da feita em js/login.js
    $erros = validarCadastro($valores, $senha, $confirmar, $termos);
    if (empty($erros['email']) && emailCadastrado($valores['email'])) {
        $erros['email'] = 'Este e-mail já está cadastrado. Entre com sua senha.';
    }

    if (!$erros) {
        cadastrarUsuario($valores, $senha);
        $primeiroNome = explode(' ', $valores['nome'])[0];
        definirMensagem('sucesso', "Conta criada, {$primeiroNome}! Agora entre com seu e-mail e senha.");
        redirecionar('login.php#entrar');
    }
}

$titulo = 'Entrar ou cadastrar-se | Clone Airbnb';
$descricao = 'Entre ou crie sua conta no clone educacional do Airbnb.';
$pagina = 'login';
$cabecalho = 'compacto';
$mensagemNoTopo = false; // a mensagem aparece dentro do cartão
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="login-fundo">
        <!-- Mosaico decorativo de fotos (alt vazio porque é só decoração) -->
        <div class="login-mosaico" aria-hidden="true">
            <img src="img/acomodacoes/casa-03.jpg" alt=""><img src="img/acomodacoes/casa-04.jpg" alt="">
            <img src="img/acomodacoes/casa-06.jpg" alt=""><img src="img/acomodacoes/casa-08.jpg" alt="">
            <img src="img/acomodacoes/casa-11.jpg" alt=""><img src="img/acomodacoes/casa-19.jpg" alt="">
            <img src="img/acomodacoes/casa-22.jpg" alt=""><img src="img/acomodacoes/casa-24.jpg" alt="">
            <img src="img/acomodacoes/casa-13.jpg" alt=""><img src="img/acomodacoes/casa-05.jpg" alt="">
            <img src="img/acomodacoes/casa-18.jpg" alt=""><img src="img/acomodacoes/casa-07.jpg" alt="">
        </div>

        <section class="login-cartao" aria-labelledby="titulo-login">
            <h1 id="titulo-login">Entrar ou cadastrar-se</h1>

            <div class="abas-form" role="tablist" aria-label="Escolha entre entrar e cadastrar">
                <button type="button" role="tab" id="aba-entrar" aria-controls="form-login" aria-selected="<?= $abaAtiva === 'entrar' ? 'true' : 'false' ?>"<?= $abaAtiva === 'entrar' ? '' : ' tabindex="-1"' ?>>Entrar</button>
                <button type="button" role="tab" id="aba-cadastro" aria-controls="form-cadastro" aria-selected="<?= $abaAtiva === 'cadastro' ? 'true' : 'false' ?>"<?= $abaAtiva === 'cadastro' ? '' : ' tabindex="-1"' ?>>Cadastrar-se</button>
            </div>

            <?= exibirMensagem() ?>

            <!-- Formulário 1: login (POST para o próprio login.php) -->
            <form class="form-pilha" id="form-login" role="tabpanel" aria-labelledby="aba-entrar" action="login.php" method="post"<?= $abaAtiva === 'entrar' ? '' : ' hidden' ?>>
                <input type="hidden" name="acao" value="entrar">
                <p class="alerta alerta-erro" data-resumo role="alert"<?= $erroLogin ? '' : ' hidden' ?>><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span><?= e($erroLogin) ?></span></p>
                <div class="campo">
                    <label for="login-email">E-mail</label>
                    <input type="email" id="login-email" name="email" required autocomplete="email" placeholder="nome@exemplo.com" data-nome="O e-mail" value="<?= e($emailLogin) ?>">
                </div>
                <div class="campo">
                    <label for="login-senha">Senha</label>
                    <div class="senha-wrapper">
                        <input type="password" id="login-senha" name="senha" required minlength="8" autocomplete="current-password" data-nome="A senha">
                        <button type="button" class="icon-btn" data-mostrar-senha="login-senha" aria-label="Mostrar senha" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                    </div>
                </div>
                <label style="display:flex;gap:8px;align-items:center;font-size:.875rem">
                    <input type="checkbox" name="lembrar" style="accent-color:var(--cor-texto)"> Manter conectado
                </label>
                <button type="submit" class="btn btn-primario btn-bloco">Continuar</button>
                <a href="#" class="link-sublinhado" style="font-size:.875rem;text-align:center">Esqueceu a senha?</a>
            </form>

            <!-- Formulário 2: cadastro (POST para o próprio login.php) -->
            <form class="form-pilha" id="form-cadastro" role="tabpanel" aria-labelledby="aba-cadastro" action="login.php#cadastro" method="post"<?= $abaAtiva === 'cadastro' ? '' : ' hidden' ?>>
                <input type="hidden" name="acao" value="cadastrar">
                <p class="alerta alerta-erro" data-resumo role="alert"<?= $abaAtiva === 'cadastro' && $erros ? '' : ' hidden' ?>><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span><?= $abaAtiva === 'cadastro' && $erros ? 'Corrija os campos destacados para continuar.' : '' ?></span></p>
                <div class="campo">
                    <label for="cad-nome">Nome completo</label>
                    <input type="text" id="cad-nome" name="nome" required minlength="3" maxlength="80" autocomplete="name" value="<?= e($valores['nome']) ?>"
                        pattern="[A-Za-zÀ-ÿ' ]+ [A-Za-zÀ-ÿ' ]+" data-msg-pattern="Informe nome e sobrenome, apenas com letras." data-nome="O nome"<?= invalido($erros, 'nome', 'cad-nome') ?>>
                    <?= erroCampo($erros, 'nome', 'cad-nome') ?>
                </div>
                <div class="campo">
                    <label for="cad-email">E-mail</label>
                    <input type="email" id="cad-email" name="email" required maxlength="120" autocomplete="email" data-nome="O e-mail" value="<?= e($valores['email']) ?>"<?= invalido($erros, 'email', 'cad-email') ?>>
                    <?= erroCampo($erros, 'email', 'cad-email') ?>
                </div>
                <div class="campo-linha">
                    <div class="campo">
                        <label for="cad-telefone">Celular</label>
                        <input type="tel" id="cad-telefone" name="telefone" required data-mascara="telefone" placeholder="(49) 99999-9999" value="<?= e($valores['telefone']) ?>"
                            pattern="\(\d{2}\) \d{4,5}-\d{4}" data-msg-pattern="Use o formato (49) 99999-9999." autocomplete="tel" data-nome="O celular"<?= invalido($erros, 'telefone', 'cad-telefone') ?>>
                        <?= erroCampo($erros, 'telefone', 'cad-telefone') ?>
                    </div>
                    <div class="campo">
                        <label for="cad-nascimento">Data de nascimento</label>
                        <input type="date" id="cad-nascimento" name="nascimento" required min="1900-01-01" value="<?= e($valores['nascimento']) ?>"
                            data-msg-max="Você precisa ter 18 anos ou mais para se cadastrar." data-nome="A data de nascimento"<?= invalido($erros, 'nascimento', 'cad-nascimento') ?>>
                        <?= erroCampo($erros, 'nascimento', 'cad-nascimento') ?>
                    </div>
                </div>
                <div class="campo">
                    <label for="cad-senha">Senha</label>
                    <div class="senha-wrapper">
                        <input type="password" id="cad-senha" name="senha" required minlength="8" maxlength="72" autocomplete="new-password"
                            pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" data-msg-pattern="A senha precisa ter letras e números." data-nome="A senha"
                            aria-describedby="dica-senha<?= isset($erros['senha']) ? ' cad-senha-erro' : '' ?>"<?= isset($erros['senha']) ? ' aria-invalid="true"' : '' ?>>
                        <button type="button" class="icon-btn" data-mostrar-senha="cad-senha" aria-label="Mostrar senha" aria-pressed="false"><i class="bi bi-eye" aria-hidden="true"></i></button>
                    </div>
                    <p class="dica" id="dica-senha">Mínimo de 8 caracteres, com letras e números.</p>
                    <?= erroCampo($erros, 'senha', 'cad-senha') ?>
                </div>
                <div class="campo">
                    <label for="cad-confirmar">Confirmar senha</label>
                    <input type="password" id="cad-confirmar" name="confirmar" required autocomplete="new-password" data-nome="A confirmação"<?= invalido($erros, 'confirmar', 'cad-confirmar') ?>>
                    <?= erroCampo($erros, 'confirmar', 'cad-confirmar') ?>
                </div>
                <label style="display:flex;gap:8px;align-items:flex-start;font-size:.875rem" class="campo">
                    <span style="display:flex;gap:8px"><input type="checkbox" name="termos" id="cad-termos" required style="accent-color:var(--cor-texto);width:auto;min-height:0"<?= invalido($erros, 'termos', 'cad-termos') ?>>
                    Concordo com os Termos de Serviço e a Política de Privacidade (fictícios).</span>
                    <?= erroCampo($erros, 'termos', 'cad-termos') ?>
                </label>
                <button type="submit" class="btn btn-primario btn-bloco">Concordar e continuar</button>
            </form>

            <p class="separador-ou">ou</p>
            <div class="login-social">
                <button type="button" class="icon-btn" aria-label="Continuar com Google (indisponível no clone)"><i class="bi bi-google" aria-hidden="true"></i></button>
                <button type="button" class="icon-btn" aria-label="Continuar com Apple (indisponível no clone)"><i class="bi bi-apple" aria-hidden="true"></i></button>
                <button type="button" class="icon-btn" aria-label="Continuar com Facebook (indisponível no clone)"><i class="bi bi-facebook" aria-hidden="true"></i></button>
            </div>
        </section>
    </main>

<?php
$scripts = ['validacao', 'login'];
require 'includes/rodape.php';
