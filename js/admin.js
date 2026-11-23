/* =========================================================================
   admin.js — Comportamentos do painel administrativo
   - Validação no cliente dos formulários do painel (validacao.js)
   - Confirmação de exclusão em um <dialog> (sem alert/confirm)
   - Formulário de acomodação: prévia das fotos e regras extras
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    /* Login do painel: valida no cliente e envia ao PHP */
    const login = document.getElementById('form-admin-login');
    if (login) validarFormulario(login, { aoEnviar: form => form.submit() });

    /* Formulário de acomodação (cadastrar/editar) */
    const formAcomodacao = document.getElementById('form-acomodacao');
    if (formAcomodacao) {
        const selectsFoto = [...formAcomodacao.querySelectorAll('select[name="imagens[]"]')];

        // troca a miniatura ao escolher a foto
        selectsFoto.forEach(select => select.addEventListener('change', () => {
            const previa = formAcomodacao.querySelector(`[data-previa="${select.id}"]`);
            previa.src = select.value ? `../img/acomodacoes/${select.value}.jpg` : '../img/favicon.svg';
        }));

        validarFormulario(formAcomodacao, {
            regras: {
                // mesma regra do PHP: no máximo 2 hóspedes por cama
                capacidade: (valor, f) => Number(valor) > 2 * Number(f.elements.camas.value)
                    ? 'A capacidade não pode passar de 2 hóspedes por cama.' : '',
                'imagens[]': valor => selectsFoto.filter(s => s.value === valor).length > 1
                    ? 'Esta foto já foi escolhida.' : ''
            },
            aoEnviar: form => form.submit()
        });
    }

    /* Exclusão: o formulário só é enviado depois da confirmação no modal */
    const modal = document.getElementById('modal-excluir');
    if (modal) {
        let formAtual = null;
        document.querySelectorAll('.form-excluir').forEach(form => {
            form.addEventListener('submit', evento => {
                evento.preventDefault();
                formAtual = form;
                document.getElementById('excluir-nome').textContent = form.dataset.titulo;
                modal.showModal();
                document.getElementById('confirmar-excluir').focus();
            });
        });
        document.getElementById('confirmar-excluir').addEventListener('click', () => {
            if (formAtual) formAtual.submit();
        });
        modal.addEventListener('click', evento => {
            if (evento.target.closest('[data-fechar]') || evento.target === modal) modal.close();
        });
        modal.addEventListener('close', () => {
            if (formAtual) formAtual.querySelector('button').focus();
        });
    }
});
