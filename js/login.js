/* =========================================================================
   login.js — Página "Entrar ou cadastrar-se"
   - Alterna entre os formulários de login e cadastro (abas)
   - Mostrar/ocultar senha
   - Validação no cliente com mensagens na página (validacao.js)
   - Simula a sessão salvando o usuário no localStorage
     (na Parte 3 isso será feito com $_SESSION no PHP)
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    const abas = [document.getElementById('aba-entrar'), document.getElementById('aba-cadastro')];
    const forms = [document.getElementById('form-login'), document.getElementById('form-cadastro')];
    const sucesso = document.getElementById('login-sucesso');

    /* ---------- Abas ---------- */
    function selecionarAba(indice, focar = false) {
        abas.forEach((aba, i) => {
            aba.setAttribute('aria-selected', i === indice);
            aba.tabIndex = i === indice ? 0 : -1;
            forms[i].hidden = i !== indice;
        });
        if (focar) abas[indice].focus();
        history.replaceState(null, '', indice ? '#cadastro' : '#entrar');
    }
    abas.forEach((aba, i) => {
        aba.addEventListener('click', () => selecionarAba(i));
        aba.addEventListener('keydown', evento => {
            if (evento.key === 'ArrowRight' || evento.key === 'ArrowLeft') selecionarAba(1 - i, true);
        });
    });
    // o link "Cadastrar-se" do menu abre direto a aba de cadastro
    if (location.hash === '#cadastro') selecionarAba(1);

    /* ---------- Mostrar/ocultar senha ---------- */
    document.querySelectorAll('[data-mostrar-senha]').forEach(botao => {
        botao.addEventListener('click', () => {
            const campo = document.getElementById(botao.dataset.mostrarSenha);
            const mostrando = campo.type === 'text';
            campo.type = mostrando ? 'password' : 'text';
            botao.setAttribute('aria-pressed', !mostrando);
            botao.setAttribute('aria-label', mostrando ? 'Mostrar senha' : 'Ocultar senha');
            botao.querySelector('i').className = mostrando ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    });

    /* Mensagem de sucesso + redirecionamento para a página inicial */
    function entrar(nome, mensagem) {
        gravarStorage('cloneAirbnb.usuario', { nome });
        forms.forEach(f => { f.querySelectorAll('button[type="submit"]').forEach(b => { b.disabled = true; }); });
        mostrarSucesso(sucesso, mensagem + ' Redirecionando...');
        setTimeout(() => { window.location.href = 'index.html'; }, 1800);
    }

    /* ---------- Formulário de login ---------- */
    validarFormulario(forms[0], {
        aoEnviar: form => {
            const email = form.elements.email.value.trim();
            const nome = email.split('@')[0].replace(/[._-]+/g, ' ');
            entrar(nome.charAt(0).toUpperCase() + nome.slice(1), 'Login realizado com sucesso!');
        }
    });

    /* ---------- Formulário de cadastro ---------- */
    const nascimento = document.getElementById('cad-nascimento');
    nascimento.max = dataMaxima18(); // só maiores de 18 anos

    validarFormulario(forms[1], {
        regras: {
            confirmar: (valor, form) => valor !== form.elements.senha.value ? 'As senhas não conferem.' : ''
        },
        aoEnviar: form => {
            const nome = form.elements.nome.value.trim();
            entrar(nome, `Conta criada! Boas-vindas, ${nome.split(' ')[0]}.`);
        }
    });

    aplicarMascaras();
});
