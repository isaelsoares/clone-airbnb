/* =========================================================================
   anfitriao.js — "Configure seu anúncio": modal com o formulário de endereço
   ========================================================================= */

const ESTADOS = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB',
    'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-endereco');
    const form = document.getElementById('form-endereco');
    const gatilho = document.getElementById('abrir-endereco');
    const sucesso = document.getElementById('anuncio-sucesso');

    // opções de estado geradas a partir do array
    form.elements.estado.insertAdjacentHTML('beforeend', ESTADOS.map(uf => `<option value="${uf}">${uf}</option>`).join(''));
    form.elements.disponivel.min = paraISO(hoje());

    gatilho.addEventListener('click', () => {
        modal.showModal();
        form.elements.cep.focus();
    });
    modal.addEventListener('click', evento => {
        if (evento.target.closest('[data-fechar]') || evento.target === modal) modal.close();
    });
    modal.addEventListener('close', () => gatilho.focus());

    validarFormulario(form, {
        regras: {
            // só aceita CEP de Santa Catarina quando o estado escolhido é SC (exemplo de regra cruzada)
            cep: (valor, f) => f.elements.estado.value === 'SC' && !/^(88|89)/.test(valor)
                ? 'CEPs de Santa Catarina começam com 88 ou 89.' : ''
        },
        aoEnviar: f => {
            const resumo = `${f.elements.endereco.value.trim()}, ${f.elements.cidade.value.trim()} - ${f.elements.estado.value}`;
            document.getElementById('resumo-endereco').textContent = resumo;
            modal.close();
            mostrarSucesso(sucesso, `Endereço confirmado! Seu anúncio para até ${f.elements.capacidade.value} hóspede(s) ` +
                `por ${formatarMoeda(Number(f.elements.diaria.value))} a noite foi salvo como rascunho.`);
        }
    });

    aplicarMascaras();
});
