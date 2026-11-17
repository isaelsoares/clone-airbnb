/* =========================================================================
   validacao.js — Validação de formulários no cliente
   Usa a Constraint Validation API (required, type, min, max, pattern...)
   e exibe as mensagens de erro na própria página, abaixo de cada campo
   (sem alert). Também permite regras personalizadas por campo.

   Uso:
     validarFormulario(form, {
         regras: { nomeDoCampo: (valor, form) => 'mensagem de erro' ou '' },
         aoEnviar: form => { ... }   // chamado só quando tudo é válido
     });
   ========================================================================= */

/* Mensagens em português para cada tipo de erro nativo */
function mensagemPadrao(campo) {
    const v = campo.validity;
    const rotulo = campo.dataset.nome || 'Este campo';
    if (v.valueMissing) return campo.type === 'radio' || campo.type === 'checkbox'
        ? 'Selecione uma opção.'
        : `${rotulo} é obrigatório.`;
    if (v.typeMismatch && campo.type === 'email') return 'Informe um e-mail válido (ex.: nome@dominio.com).';
    if (v.typeMismatch) return 'Formato inválido.';
    if (v.patternMismatch) return campo.dataset.msgPattern || 'Formato inválido.';
    if (v.tooShort) return `Use pelo menos ${campo.minLength} caracteres.`;
    if (v.tooLong) return `Use no máximo ${campo.maxLength} caracteres.`;
    if (v.rangeUnderflow) return campo.dataset.msgMin || `O valor mínimo é ${campo.min}.`;
    if (v.rangeOverflow) return campo.dataset.msgMax || `O valor máximo é ${campo.max}.`;
    if (v.badInput) return 'Valor inválido.';
    return '';
}

/* Cria (se necessário) o <p> de erro ligado ao campo via aria-describedby */
function elementoErro(campo) {
    const id = (campo.id || campo.name) + '-erro';
    let erro = document.getElementById(id);
    if (!erro) {
        erro = document.createElement('p');
        erro.id = id;
        erro.className = 'erro-campo';
        erro.setAttribute('aria-live', 'polite');
        const container = campo.closest('.campo') || campo.parentElement;
        container.appendChild(erro);
    }
    const descricoes = (campo.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
    if (!descricoes.includes(id)) campo.setAttribute('aria-describedby', [...descricoes, id].join(' '));
    return erro;
}

function mostrarErro(campo, mensagem) {
    const erro = elementoErro(campo);
    erro.textContent = mensagem;
    campo.setAttribute('aria-invalid', mensagem ? 'true' : 'false');
}

/* Valida um único campo: primeiro as regras nativas, depois a regra personalizada */
function validarCampo(campo, form, regras) {
    campo.setCustomValidity('');
    let mensagem = mensagemPadrao(campo);
    if (!mensagem && regras[campo.name]) mensagem = regras[campo.name](campo.value.trim(), form) || '';
    campo.setCustomValidity(mensagem);
    mostrarErro(campo, mensagem);
    return !mensagem;
}

function validarFormulario(form, { regras = {}, aoEnviar } = {}) {
    form.noValidate = true; // desliga os balões nativos: as mensagens são nossas
    let tentouEnviar = false;

    // campos que participam da validação (ignora botões e campos ocultos/desabilitados)
    const campos = () => [...form.elements].filter(el =>
        el.name && !el.disabled && el.type !== 'submit' && el.type !== 'button' && !el.closest('[hidden]'));

    // valida ao sair do campo; depois da 1ª tentativa, valida também enquanto digita
    form.addEventListener('focusout', evento => {
        if (campos().includes(evento.target) && evento.target.value) validarCampo(evento.target, form, regras);
    });
    form.addEventListener('input', evento => {
        if (tentouEnviar && campos().includes(evento.target)) validarCampo(evento.target, form, regras);
    });

    form.addEventListener('submit', evento => {
        evento.preventDefault();
        tentouEnviar = true;
        let primeiroInvalido = null;
        campos().forEach(campo => {
            if (!validarCampo(campo, form, regras) && !primeiroInvalido) primeiroInvalido = campo;
        });

        const resumo = form.querySelector('.alerta-erro[data-resumo]');
        if (primeiroInvalido) {
            if (resumo) {
                resumo.hidden = false;
                resumo.querySelector('span').textContent = 'Corrija os campos destacados para continuar.';
            }
            primeiroInvalido.focus();
            return;
        }
        if (resumo) resumo.hidden = true;
        if (aoEnviar) aoEnviar(form);
    });
}

/* Exibe a mensagem de sucesso de um formulário */
function mostrarSucesso(elemento, texto) {
    elemento.hidden = false;
    elemento.querySelector('span').textContent = texto;
    elemento.setAttribute('tabindex', '-1');
    elemento.focus();
}

/* ---------- Máscaras de digitação ---------- */
const MASCARAS = {
    cpf: v => v.replace(/\D/g, '').slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'),
    cep: v => v.replace(/\D/g, '').slice(0, 8).replace(/(\d{5})(\d)/, '$1-$2'),
    telefone: v => v.replace(/\D/g, '').slice(0, 11)
        .replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d)(\d{4})$/, '$1-$2'),
    cartao: v => v.replace(/\D/g, '').slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ')
};

function aplicarMascaras(raiz = document) {
    raiz.querySelectorAll('[data-mascara]').forEach(campo => {
        const mascara = MASCARAS[campo.dataset.mascara];
        if (mascara) campo.addEventListener('input', () => { campo.value = mascara(campo.value); });
    });
}

/* Valida CPF pelo cálculo dos dígitos verificadores */
function cpfValido(cpf) {
    const n = cpf.replace(/\D/g, '');
    if (n.length !== 11 || /^(\d)\1+$/.test(n)) return false;
    for (let t = 9; t < 11; t++) {
        let soma = 0;
        for (let i = 0; i < t; i++) soma += Number(n[i]) * (t + 1 - i);
        const digito = ((soma * 10) % 11) % 10;
        if (digito !== Number(n[t])) return false;
    }
    return true;
}

/* Calcula a idade a partir de "AAAA-MM-DD" */
function idade(dataISO) {
    const nasc = paraData(dataISO);
    if (!nasc) return 0;
    const h = hoje();
    let anos = h.getFullYear() - nasc.getFullYear();
    if (h < new Date(h.getFullYear(), nasc.getMonth(), nasc.getDate())) anos--;
    return anos;
}

/* Data máxima para quem tem pelo menos 18 anos (usada no atributo max) */
function dataMaxima18() {
    const h = hoje();
    return paraISO(new Date(h.getFullYear() - 18, h.getMonth(), h.getDate()));
}
