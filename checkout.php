<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$titulo = 'Confirmar e pagar | Clone Airbnb';
$descricao = 'Confirme e pague sua reserva.';
$pagina = 'checkout';
$cabecalho = 'simples';
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container checkout">
        <div class="checkout-titulo">
            <a href="#" class="icon-btn" id="voltar-anuncio" aria-label="Voltar para o anúncio"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
            <h1>Confirmar e pagar</h1>
        </div>

        <div class="checkout-colunas" id="checkout-colunas">
            <!-- Formulário de pagamento -->
            <form id="form-pagamento" action="checkout.php" method="post" class="form-pilha" style="gap:16px">
                <p class="alerta alerta-erro" data-resumo hidden><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span></span></p>

                <section class="etapa ativa" aria-labelledby="etapa-1">
                    <h2 id="etapa-1">1. Adicione uma forma de pagamento</h2>
                    <p>Formas de pagamento disponíveis para BRL.</p>

                    <fieldset style="border:0;padding:0;margin:0" class="campo">
                        <legend class="visually-hidden">Forma de pagamento</legend>
                        <label class="opcao-pagamento">
                            <i class="bi bi-qr-code" aria-hidden="true"></i>
                            <span>Pix<small>Parcelamento não disponível</small></span>
                            <input type="radio" name="forma" value="pix" checked required>
                        </label>
                        <hr class="divisor">
                        <label class="opcao-pagamento">
                            <i class="bi bi-credit-card" aria-hidden="true"></i>
                            <span>Cartão de crédito<small id="texto-parcelas">ou em até 12x</small>
                                <span class="bandeiras" aria-hidden="true"><i class="bi bi-credit-card-2-front"></i><i class="bi bi-credit-card-2-back"></i><i class="bi bi-wallet2"></i></span></span>
                            <input type="radio" name="forma" value="cartao">
                        </label>
                    </fieldset>

                    <!-- Dados do cartão (aparecem só quando "Cartão" está marcado) -->
                    <div class="grupo-campos campos-cartao" id="campos-cartao" hidden>
                        <div class="campo">
                            <label for="pag-cartao">Número do cartão</label>
                            <input type="text" id="pag-cartao" name="cartao" required inputmode="numeric" data-mascara="cartao" autocomplete="cc-number"
                                pattern="\d{4} \d{4} \d{4} \d{4}" data-msg-pattern="O cartão deve ter 16 dígitos." placeholder="0000 0000 0000 0000" data-nome="O número do cartão">
                        </div>
                        <div class="campo-linha">
                            <div class="campo">
                                <label for="pag-validade">Validade</label>
                                <input type="month" id="pag-validade" name="validade" required autocomplete="cc-exp" data-msg-min="Este cartão está vencido." data-nome="A validade">
                            </div>
                            <div class="campo">
                                <label for="pag-cvv">CVV</label>
                                <input type="text" id="pag-cvv" name="cvv" required inputmode="numeric" maxlength="4" pattern="\d{3,4}"
                                    data-msg-pattern="O CVV tem 3 ou 4 dígitos." autocomplete="cc-csc" data-nome="O CVV">
                            </div>
                        </div>
                    </div>

                    <h3>Informações fiscais</h3>
                    <p class="texto-suave" style="font-size:.875rem;margin-top:-10px">Digite suas informações exatamente como aparecem em seu documento de identificação.</p>
                    <div class="grupo-campos">
                        <div class="campo">
                            <label for="pag-nome">Nome legal completo</label>
                            <input type="text" id="pag-nome" name="nome" required minlength="5" autocomplete="name"
                                pattern="[A-Za-zÀ-ÿ' ]+ [A-Za-zÀ-ÿ' ]+" data-msg-pattern="Informe nome e sobrenome." data-nome="O nome">
                        </div>
                        <div class="campo-linha">
                            <div class="campo">
                                <label for="pag-cpf">CPF</label>
                                <input type="text" id="pag-cpf" name="cpf" required inputmode="numeric" data-mascara="cpf" placeholder="000.000.000-00"
                                    pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" data-msg-pattern="Use o formato 000.000.000-00." data-nome="O CPF">
                            </div>
                            <div class="campo">
                                <label for="pag-nascimento">Data de nascimento</label>
                                <input type="date" id="pag-nascimento" name="nascimento" required min="1900-01-01"
                                    data-msg-max="O titular da reserva precisa ter 18 anos ou mais." data-nome="A data de nascimento">
                            </div>
                        </div>
                        <div class="campo">
                            <label for="pag-email">E-mail para a confirmação</label>
                            <input type="email" id="pag-email" name="email" required autocomplete="email" data-nome="O e-mail">
                        </div>
                    </div>
                </section>

                <section class="etapa" aria-labelledby="etapa-2" id="etapa-parcelas">
                    <h2 id="etapa-2">2. Selecione o número de parcelas</h2>
                    <div class="campo">
                        <label for="pag-parcelas">Parcelas</label>
                        <select id="pag-parcelas" name="parcelas" disabled></select>
                        <p class="dica" id="dica-parcelas">Disponível apenas para cartão de crédito.</p>
                    </div>
                </section>

                <section class="etapa" aria-labelledby="etapa-3">
                    <h2 id="etapa-3">3. Revise sua reserva</h2>
                    <p class="texto-suave" style="font-size:.875rem">Ao selecionar o botão abaixo, concordo com as Regras da Casa, as Regras básicas para hóspedes e a Política de cancelamento (fictícias).</p>
                    <button type="submit" class="btn btn-primario btn-bloco" id="btn-confirmar">Confirmar e pagar</button>
                </section>
            </form>

            <!-- Resumo da reserva -->
            <aside class="resumo" aria-labelledby="titulo-resumo">
                <p class="aviso-tempo" role="timer" aria-live="off"><i class="bi bi-alarm" aria-hidden="true"></i><span>Você só tem mais <strong id="tempo-restante">4:00:00</strong> para reservar suas datas</span></p>
                <div class="resumo-card">
                    <div class="resumo-acomodacao">
                        <img id="resumo-foto" src="" alt="">
                        <div>
                            <h2 id="titulo-resumo" style="font-size:1rem;font-weight:500"></h2>
                            <p class="texto-suave" id="resumo-nota" style="font-size:.875rem"></p>
                        </div>
                    </div>
                    <p style="font-size:.875rem">Cancelamento gratuito por 48 horas. <a href="#" class="link-sublinhado">Política completa</a></p>
                    <hr class="divisor">
                    <div class="resumo-linha"><div><strong>Datas</strong><span id="resumo-datas"></span></div><a class="btn btn-secundario btn-pequeno" id="alterar-datas" href="#">Alterar</a></div>
                    <hr class="divisor">
                    <div class="resumo-linha"><div><strong>Hóspedes</strong><span id="resumo-hospedes"></span></div><a class="btn btn-secundario btn-pequeno" id="alterar-hospedes" href="#">Alterar</a></div>
                    <hr class="divisor">
                    <h3 style="font-size:.875rem">Informações de preço</h3>
                    <table class="tabela-preco" id="tabela-checkout">
                        <caption class="visually-hidden">Detalhamento do preço</caption>
                        <tbody></tbody>
                        <tfoot><tr><td>Total (BRL)</td><td id="checkout-total"></td></tr></tfoot>
                    </table>
                </div>
            </aside>
        </div>

        <!-- Exibido após a confirmação -->
        <section class="etapa confirmacao" id="confirmacao" aria-labelledby="titulo-confirmacao" tabindex="-1" hidden>
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <h2 id="titulo-confirmacao">Reserva confirmada!</h2>
            <p id="texto-confirmacao"></p>
            <a href="index.php" class="btn btn-escuro">Voltar ao início</a>
        </section>
    </main>

<?php
$scripts = ['validacao', 'checkout'];
require 'includes/rodape.php';
