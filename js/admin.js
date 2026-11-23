/* =========================================================================
   admin.js — Comportamentos do painel administrativo
   - Validação no cliente dos formulários do painel (validacao.js)
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    /* Login do painel: valida no cliente e envia ao PHP */
    const login = document.getElementById('form-admin-login');
    if (login) validarFormulario(login, { aoEnviar: form => form.submit() });
});
