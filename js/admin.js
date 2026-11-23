/* =========================================================================
   admin.js — Comportamentos do painel administrativo
   - Validação no cliente dos formulários do painel (validacao.js)
   - Confirmação de exclusão em um <dialog> (sem alert/confirm)
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    /* Login do painel: valida no cliente e envia ao PHP */
    const login = document.getElementById('form-admin-login');
    if (login) validarFormulario(login, { aoEnviar: form => form.submit() });

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
