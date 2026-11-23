<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';
require_once 'includes/dados.php';

/* ---------- Reserva confirmada (Post/Redirect/Get) ---------- */
$confirmada = null;
if (textoGet('confirmada') !== '') {
    $confirmada = $_SESSION['reserva_confirmada'] ?? null;
    unset($_SESSION['reserva_confirmada']);
    if (!$confirmada) {
        redirecionar('index.php'); // página recarregada depois da confirmação
    }
}

if (!$confirmada) {
    /* Os dados da reserva chegam por GET (vindo do anúncio) e voltam por POST
       em campos ocultos do formulário de pagamento */
    $enviado = $_SERVER['REQUEST_METHOD'] === 'POST';
    $origem = $enviado ? INPUT_POST : INPUT_GET;
    $ler = fn(string $campo): string => $enviado ? textoPost($campo) : textoGet($campo);

    $id = inteiro($origem, 'id', 1);
    $acomodacao = $id ? buscarAcomodacao($id) : null;
    if (!$acomodacao) {
        definirMensagem('erro', 'Escolha uma acomodação para fazer a reserva.');
        redirecionar('acomodacoes.php');
    }

    // página aberta direto, sem datas: sugere daqui a 14 dias (como no anúncio)
    $checkin = $ler('checkin') ?: hoje()->modify('+14 days')->format('Y-m-d');
    $checkout = $ler('checkout');
    if ($checkout === '' && dataValida($checkin)) {
        $checkout = dataValida($checkin)->modify('+' . max($acomodacao['minNoites'], 2) . ' days')->format('Y-m-d');
    }
    $hospedes = $ler('hospedes') === '' ? 1 : inteiro($origem, 'hospedes', 1);

    // RN1 e RN2 conferidas no servidor (a URL pode ter sido editada)
    $errosReserva = validarReserva($acomodacao, $checkin, $checkout, $hospedes);
    // RN3: o total é sempre calculado aqui, nunca recebido do navegador
    $valores = calcularReserva($acomodacao, max(contarNoites($checkin, $checkout), 0));

    $linkAnuncio = 'anuncio.php?' . http_build_query([
        'id' => $acomodacao['id'], 'checkin' => $checkin, 'checkout' => $checkout, 'hospedes' => $hospedes,
    ]);

    $erros = [];
    $dados = ['forma' => 'pix', 'validade' => '', 'parcelas' => '1', 'nome' => '', 'cpf' => '', 'nascimento' => '', 'email' => ''];

    if ($enviado) {
        $dados = [
            'forma' => textoPost('forma'),
            'validade' => textoPost('validade'),
            'parcelas' => textoPost('parcelas') ?: '1',
            'nome' => preg_replace('/\s+/', ' ', textoPost('nome')),
            'cpf' => textoPost('cpf'),
            'nascimento' => textoPost('nascimento'),
            'email' => mb_strtolower(textoPost('email')),
        ];
        $erros = validarPagamento($dados, somenteDigitos(textoPost('cartao')), textoPost('cvv'));

        if (!$errosReserva && !$erros) {
            $reservaId = registrarReserva([
                'acomodacao_id' => $acomodacao['id'],
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'cpf' => $dados['cpf'],
                'checkin' => $checkin,
                'checkout' => $checkout,
                'hospedes' => $hospedes,
                'forma' => $dados['forma'],
                'total' => $valores['total'],
            ]);
            $_SESSION['reserva_confirmada'] = [
                'codigo' => codigoReserva($reservaId),
                'titulo' => $acomodacao['titulo'],
                'checkin' => $checkin,
                'checkout' => $checkout,
                'total' => $valores['total'],
                'pagamento' => $dados['forma'] === 'cartao' ? 'em ' . (int) $dados['parcelas'] . 'x no cartão' : 'via Pix',
                'email' => $dados['email'],
            ];
            redirecionar('checkout.php?confirmada=1');
        }
    }
    $cartao = $dados['forma'] === 'cartao';
}

$titulo = 'Confirmar e pagar | Clone Airbnb';
$descricao = 'Confirme e pague sua reserva.';
$pagina = 'checkout';
$cabecalho = 'simples';
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container checkout">
<?php if ($confirmada): ?>
        <!-- Reserva gravada no banco -->
        <section class="etapa confirmacao" id="confirmacao" aria-labelledby="titulo-confirmacao" tabindex="-1">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <h1 id="titulo-confirmacao">Reserva confirmada!</h1>
            <p>
                Código <strong><?= e($confirmada['codigo']) ?></strong> · <?= e($confirmada['titulo']) ?><br>
                <?= formatarData($confirmada['checkin']) ?> – <?= formatarData($confirmada['checkout']) ?> · <?= formatarMoeda($confirmada['total']) ?> <?= e($confirmada['pagamento']) ?><br>
                Enviamos os detalhes para <?= e($confirmada['email']) ?> (simulação).
            </p>
            <a href="index.php" class="btn btn-escuro">Voltar ao início</a>
        </section>
<?php else: ?>
        <div class="checkout-titulo">
            <a href="<?= e($linkAnuncio) ?>" class="icon-btn" id="voltar-anuncio" aria-label="Voltar para o anúncio"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
            <h1>Confirmar e pagar</h1>
        </div>

        <div class="checkout-colunas" id="checkout-colunas">
            <!-- Formulário de pagamento (POST para o próprio checkout.php) -->
            <form id="form-pagamento" action="checkout.php" method="post" class="form-pilha" style="gap:16px">
                <!-- dados da reserva escolhidos no anúncio -->
                <input type="hidden" name="id" value="<?= $acomodacao['id'] ?>">
                <input type="hidden" name="checkin" value="<?= e($checkin) ?>">
                <input type="hidden" name="checkout" value="<?= e($checkout) ?>">
                <input type="hidden" name="hospedes" value="<?= e($hospedes ?? '') ?>">

<?php if ($errosReserva): ?>
                <p class="alerta alerta-erro" role="alert"><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span><?= e(implode(' ', $errosReserva)) ?> <a href="<?= e($linkAnuncio) ?>">Voltar e alterar a reserva</a></span></p>
<?php endif; ?>
                <p class="alerta alerta-erro" data-resumo role="alert"<?= $erros ? '' : ' hidden' ?>><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span><?= $erros ? 'Corrija os campos destacados para continuar.' : '' ?></span></p>

                <section class="etapa ativa" aria-labelledby="etapa-1">
                    <h2 id="etapa-1">1. Adicione uma forma de pagamento</h2>
                    <p>Formas de pagamento disponíveis para BRL.</p>

                    <fieldset style="border:0;padding:0;margin:0" class="campo">
                        <legend class="visually-hidden">Forma de pagamento</legend>
                        <label class="opcao-pagamento">
                            <i class="bi bi-qr-code" aria-hidden="true"></i>
                            <span>Pix<small>Parcelamento não disponível</small></span>
                            <input type="radio" name="forma" value="pix" required<?= $cartao ? '' : ' checked' ?>>
                        </label>
                        <hr class="divisor">
                        <label class="opcao-pagamento">
                            <i class="bi bi-credit-card" aria-hidden="true"></i>
                            <span>Cartão de crédito<small id="texto-parcelas">ou em 12x de <?= formatarMoeda($valores['total'] / 12) ?></small>
                                <span class="bandeiras" aria-hidden="true"><i class="bi bi-credit-card-2-front"></i><i class="bi bi-credit-card-2-back"></i><i class="bi bi-wallet2"></i></span></span>
                            <input type="radio" name="forma" value="cartao"<?= $cartao ? ' checked' : '' ?>>
                        </label>
                        <?= erroCampo($erros, 'forma', 'forma') ?>
                    </fieldset>

                    <!-- Dados do cartão (aparecem só quando "Cartão" está marcado; não são gravados) -->
                    <div class="grupo-campos campos-cartao" id="campos-cartao"<?= $cartao ? '' : ' hidden' ?>>
                        <div class="campo">
                            <label for="pag-cartao">Número do cartão</label>
                            <input type="text" id="pag-cartao" name="cartao" required inputmode="numeric" data-mascara="cartao" autocomplete="cc-number"
                                pattern="\d{4} \d{4} \d{4} \d{4}" data-msg-pattern="O cartão deve ter 16 dígitos." placeholder="0000 0000 0000 0000" data-nome="O número do cartão"<?= invalido($erros, 'cartao', 'pag-cartao') ?>>
                            <?= erroCampo($erros, 'cartao', 'pag-cartao') ?>
                        </div>
                        <div class="campo-linha">
                            <div class="campo">
                                <label for="pag-validade">Validade</label>
                                <input type="month" id="pag-validade" name="validade" required autocomplete="cc-exp" min="<?= date('Y-m') ?>" value="<?= e($dados['validade']) ?>"
                                    data-msg-min="Este cartão está vencido." data-nome="A validade"<?= invalido($erros, 'validade', 'pag-validade') ?>>
                                <?= erroCampo($erros, 'validade', 'pag-validade') ?>
                            </div>
                            <div class="campo">
                                <label for="pag-cvv">CVV</label>
                                <input type="text" id="pag-cvv" name="cvv" required inputmode="numeric" maxlength="4" pattern="\d{3,4}"
                                    data-msg-pattern="O CVV tem 3 ou 4 dígitos." autocomplete="cc-csc" data-nome="O CVV"<?= invalido($erros, 'cvv', 'pag-cvv') ?>>
                                <?= erroCampo($erros, 'cvv', 'pag-cvv') ?>
                            </div>
                        </div>
                    </div>

                    <h3>Informações fiscais</h3>
                    <p class="texto-suave" style="font-size:.875rem;margin-top:-10px">Digite suas informações exatamente como aparecem em seu documento de identificação.</p>
                    <div class="grupo-campos">
                        <div class="campo">
                            <label for="pag-nome">Nome legal completo</label>
                            <input type="text" id="pag-nome" name="nome" required minlength="5" maxlength="80" autocomplete="name" value="<?= e($dados['nome']) ?>"
                                pattern="[A-Za-zÀ-ÿ' ]+ [A-Za-zÀ-ÿ' ]+" data-msg-pattern="Informe nome e sobrenome." data-nome="O nome"<?= invalido($erros, 'nome', 'pag-nome') ?>>
                            <?= erroCampo($erros, 'nome', 'pag-nome') ?>
                        </div>
                        <div class="campo-linha">
                            <div class="campo">
                                <label for="pag-cpf">CPF</label>
                                <input type="text" id="pag-cpf" name="cpf" required inputmode="numeric" data-mascara="cpf" placeholder="000.000.000-00" value="<?= e($dados['cpf']) ?>"
                                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" data-msg-pattern="Use o formato 000.000.000-00." data-nome="O CPF"<?= invalido($erros, 'cpf', 'pag-cpf') ?>>
                                <?= erroCampo($erros, 'cpf', 'pag-cpf') ?>
                            </div>
                            <div class="campo">
                                <label for="pag-nascimento">Data de nascimento</label>
                                <input type="date" id="pag-nascimento" name="nascimento" required min="1900-01-01" value="<?= e($dados['nascimento']) ?>"
                                    data-msg-max="O titular da reserva precisa ter 18 anos ou mais." data-nome="A data de nascimento"<?= invalido($erros, 'nascimento', 'pag-nascimento') ?>>
                                <?= erroCampo($erros, 'nascimento', 'pag-nascimento') ?>
                            </div>
                        </div>
                        <div class="campo">
                            <label for="pag-email">E-mail para a confirmação</label>
                            <input type="email" id="pag-email" name="email" required maxlength="120" autocomplete="email" data-nome="O e-mail" value="<?= e($dados['email']) ?>"<?= invalido($erros, 'email', 'pag-email') ?>>
                            <?= erroCampo($erros, 'email', 'pag-email') ?>
                        </div>
                    </div>
                </section>

                <section class="etapa<?= $cartao ? ' ativa' : '' ?>" aria-labelledby="etapa-2" id="etapa-parcelas">
                    <h2 id="etapa-2">2. Selecione o número de parcelas</h2>
                    <div class="campo">
                        <label for="pag-parcelas">Parcelas</label>
                        <select id="pag-parcelas" name="parcelas"<?= $cartao ? '' : ' disabled' ?><?= invalido($erros, 'parcelas', 'pag-parcelas') ?>>
<?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>"<?= (string) $i === $dados['parcelas'] ? ' selected' : '' ?>><?= $i ?>x de <?= formatarMoeda($valores['total'] / $i) ?> sem juros</option>
<?php endfor; ?>
                        </select>
                        <p class="dica" id="dica-parcelas"><?= $cartao ? 'Escolha em quantas vezes deseja pagar.' : 'Disponível apenas para cartão de crédito.' ?></p>
                        <?= erroCampo($erros, 'parcelas', 'pag-parcelas') ?>
                    </div>
                </section>

                <section class="etapa" aria-labelledby="etapa-3">
                    <h2 id="etapa-3">3. Revise sua reserva</h2>
                    <p class="texto-suave" style="font-size:.875rem">Ao selecionar o botão abaixo, concordo com as Regras da Casa, as Regras básicas para hóspedes e a Política de cancelamento (fictícias).</p>
                    <button type="submit" class="btn btn-primario btn-bloco" id="btn-confirmar"<?= $errosReserva ? ' disabled' : '' ?>>Confirmar e pagar</button>
                </section>
            </form>

            <!-- Resumo da reserva (calculado no servidor) -->
            <aside class="resumo" aria-labelledby="titulo-resumo">
                <p class="aviso-tempo" role="timer" aria-live="off"><i class="bi bi-alarm" aria-hidden="true"></i><span>Você só tem mais <strong id="tempo-restante">4:00:00</strong> para reservar suas datas</span></p>
                <div class="resumo-card">
                    <div class="resumo-acomodacao">
                        <img id="resumo-foto" src="img/acomodacoes/<?= e($acomodacao['imagens'][0] ?? 'casa-01') ?>.jpg" alt="Foto principal de <?= e($acomodacao['titulo']) ?>">
                        <div>
                            <h2 id="titulo-resumo" style="font-size:1rem;font-weight:500"><?= e($acomodacao['titulo']) ?></h2>
                            <p class="texto-suave" style="font-size:.875rem"><i class="bi bi-star-fill" aria-hidden="true"></i> <?= number_format($acomodacao['nota'], 2, ',', '') ?> (<?= $acomodacao['avaliacoes'] ?>) · <?= e($acomodacao['cidade']) ?></p>
                        </div>
                    </div>
                    <p style="font-size:.875rem">Cancelamento gratuito por 48 horas. <a href="#" class="link-sublinhado">Política completa</a></p>
                    <hr class="divisor">
                    <div class="resumo-linha"><div><strong>Datas</strong><span><?= formatarData($checkin) ?> – <?= formatarData($checkout) ?></span></div><a class="btn btn-secundario btn-pequeno" href="<?= e($linkAnuncio) ?>">Alterar</a></div>
                    <hr class="divisor">
                    <div class="resumo-linha"><div><strong>Hóspedes</strong><span><?= e($hospedes ?? '–') ?> hóspede(s)</span></div><a class="btn btn-secundario btn-pequeno" href="<?= e($linkAnuncio) ?>">Alterar</a></div>
                    <hr class="divisor">
                    <h3 style="font-size:.875rem">Informações de preço</h3>
                    <table class="tabela-preco" id="tabela-checkout">
                        <caption class="visually-hidden">Detalhamento do preço</caption>
                        <tbody>
                            <tr><td><?= $valores['noites'] ?> noite(s) x <?= formatarMoeda($acomodacao['diaria']) ?></td><td><?= formatarMoeda($valores['subtotal']) ?></td></tr>
<?php if ($valores['desconto'] > 0): ?>
                            <tr class="desconto"><td>Desconto semanal (10%)</td><td>−<?= formatarMoeda($valores['desconto']) ?></td></tr>
<?php endif; ?>
                            <tr><td>Taxa de limpeza</td><td><?= formatarMoeda($valores['limpeza']) ?></td></tr>
                            <tr><td>Taxa de serviço</td><td><?= formatarMoeda($valores['servico']) ?></td></tr>
                        </tbody>
                        <tfoot><tr><td>Total (BRL)</td><td id="checkout-total"><?= formatarMoeda($valores['total']) ?></td></tr></tfoot>
                    </table>
                </div>
            </aside>
        </div>
<?php endif; ?>
    </main>

<?php
$scripts = ['validacao', 'checkout'];
require 'includes/rodape.php';
