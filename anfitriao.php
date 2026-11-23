<?php
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

$titulo = 'Torne-se um anfitrião | Clone Airbnb';
$descricao = 'Configure seu anúncio e comece a receber hóspedes.';
$pagina = 'anfitriao';
$cabecalho = 'anfitriao';
require 'includes/cabecalho.php';
?>
    <!-- ===== CONTEÚDO ===== -->
    <main id="conteudo" class="container">
        <section class="anfitriao-hero" aria-labelledby="titulo-anfitriao">
            <div>
                <h1 id="titulo-anfitriao">Configure seu anúncio no Airbnb</h1>
                <p class="texto-suave" style="font-size:1.125rem">É fácil criar um anúncio e começar a receber hóspedes. Vamos começar.</p>
                <button type="button" class="endereco-gatilho" id="abrir-endereco" aria-haspopup="dialog">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i> <span id="resumo-endereco">Insira seu endereço</span>
                </button>
                <p class="alerta alerta-sucesso" id="anuncio-sucesso" role="status" hidden><i class="bi bi-check-circle" aria-hidden="true"></i><span></span></p>
                <p class="texto-suave">Você não está anunciando um espaço? <a href="servicos.php">Anuncie uma experiência ou serviço</a></p>
            </div>
            <div class="anfitriao-ilustracao">
                <img src="img/acomodacoes/casa-13.jpg" alt="Casa contemporânea com jardim, exemplo de espaço anunciado">
            </div>
        </section>
    </main>

    <!-- Modal "Confirme seu endereço" (formulário de cadastro do anúncio) -->
    <dialog class="modal" id="modal-endereco" aria-labelledby="titulo-endereco">
        <form id="form-endereco" action="anfitriao.php" method="post">
            <div class="modal-cabecalho">
                <button type="button" class="icon-btn btn-fechar" data-fechar aria-label="Voltar"><i class="bi bi-arrow-left" aria-hidden="true"></i></button>
                <h2 id="titulo-endereco">Confirme seu endereço</h2>
                <button type="button" class="icon-btn btn-fechar" data-fechar aria-label="Fechar"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
            </div>
            <div class="modal-corpo">
                <p class="alerta alerta-erro" data-resumo hidden><i class="bi bi-exclamation-circle" aria-hidden="true"></i><span></span></p>
                <div class="campo">
                    <label for="end-pais">País/região</label>
                    <select id="end-pais" name="pais" required data-nome="O país">
                        <option value="BR" selected>Brasil - BR</option>
                        <option value="AR">Argentina - AR</option>
                        <option value="UY">Uruguai - UY</option>
                    </select>
                </div>
                <div class="grupo-campos">
                    <div class="campo">
                        <label for="end-cep">CEP</label>
                        <input type="text" id="end-cep" name="cep" required inputmode="numeric" data-mascara="cep" placeholder="89700-000"
                            pattern="\d{5}-\d{3}" data-msg-pattern="Informe o CEP no formato 00000-000." autocomplete="postal-code" data-nome="O CEP">
                    </div>
                    <div class="campo">
                        <label for="end-rua">Endereço</label>
                        <input type="text" id="end-rua" name="endereco" required minlength="5" autocomplete="address-line1" placeholder="Rua, número" data-nome="O endereço">
                    </div>
                    <div class="campo">
                        <label for="end-apto">Apto, suíte, unidade (se aplicável)</label>
                        <input type="text" id="end-apto" name="complemento" maxlength="40" autocomplete="address-line2">
                    </div>
                    <div class="campo-linha">
                        <div class="campo">
                            <label for="end-bairro">Bairro (se aplicável)</label>
                            <input type="text" id="end-bairro" name="bairro" maxlength="60">
                        </div>
                        <div class="campo">
                            <label for="end-cidade">Cidade/município</label>
                            <input type="text" id="end-cidade" name="cidade" required minlength="2" autocomplete="address-level2" data-nome="A cidade">
                        </div>
                    </div>
                    <div class="campo">
                        <label for="end-estado">Estado</label>
                        <select id="end-estado" name="estado" required data-nome="O estado">
                            <option value="">Selecione</option>
                        </select>
                    </div>
                </div>
                <div class="campo-linha">
                    <div class="campo">
                        <label for="end-hospedes">Máximo de hóspedes</label>
                        <input type="number" id="end-hospedes" name="capacidade" required min="1" max="16" value="2" data-nome="A capacidade">
                    </div>
                    <div class="campo">
                        <label for="end-diaria">Diária (R$)</label>
                        <input type="number" id="end-diaria" name="diaria" required min="50" max="10000" step="0.01" placeholder="350,00"
                            data-msg-min="A diária mínima é R$ 50,00." data-nome="A diária">
                    </div>
                </div>
                <div class="campo">
                    <label for="end-disponivel">Disponível a partir de</label>
                    <input type="date" id="end-disponivel" name="disponivel" required data-msg-min="A data não pode estar no passado." data-nome="A data de disponibilidade">
                </div>
                <p class="alerta alerta-info"><i class="bi bi-info-circle" aria-hidden="true"></i><span>O endereço exato só é compartilhado com hóspedes que fizeram reserva.</span></p>
            </div>
            <div class="modal-rodape">
                <button type="submit" class="btn btn-escuro btn-bloco">Próximo</button>
            </div>
        </form>
    </dialog>

<?php
$scripts = ['validacao', 'anfitriao'];
require 'includes/rodape.php';
