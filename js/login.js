/**
 * Login: salvar acesso (localStorage) e preenchimento automático em modo teste
 */
(function () {
  const STORAGE_KEY = 'helpdesk_login_salvo';

  function lerSalvo() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      const dados = JSON.parse(raw);
      if (!dados || !dados.email) return null;
      return dados;
    } catch (e) {
      return null;
    }
  }

  function salvarAcesso(email, senha) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify({ email, senha }));
  }

  function limparSalvo() {
    localStorage.removeItem(STORAGE_KEY);
  }

  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.login-form');
    const usernameEl = document.getElementById('username');
    const passwordEl = document.getElementById('password');
    const rememberEl = document.getElementById('remember');

    if (!form || !usernameEl || !passwordEl || !rememberEl) return;

    const salvo = lerSalvo();

    if (salvo) {
      usernameEl.value = salvo.email;
      passwordEl.value = salvo.senha || '';
      rememberEl.checked = true;
    } else if (window.LOGIN_MODO_TESTE === 'Sim') {
      usernameEl.value = window.LOGIN_USUARIO_TESTE || '';
      passwordEl.value = window.LOGIN_SENHA_TESTE || '';
    }

    form.addEventListener('submit', function () {
      if (rememberEl.checked) {
        salvarAcesso(usernameEl.value.trim(), passwordEl.value);
      } else {
        limparSalvo();
      }
    });

    const modalRecuperar = document.getElementById('modalRecuperarSenha');
    const emailRecuperar = document.getElementById('emailRecuperar');

    if (modalRecuperar && emailRecuperar) {
      modalRecuperar.addEventListener('shown.bs.modal', function () {
        emailRecuperar.focus();
      });

      modalRecuperar.addEventListener('hidden.bs.modal', function () {
        const formRecuperar = document.getElementById('formRecuperarSenha');
        if (formRecuperar) formRecuperar.reset();
      });
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const btnEnviar = document.getElementById('btnEnviarRecuperacao');
    const formRecuperar = document.getElementById('formRecuperarSenha');
    const inputEmail = document.getElementById('emailRecuperar');

    if (btnEnviar) {
        btnEnviar.addEventListener('click', function (e) {
            e.preventDefault();

            const emailValue = inputEmail.value.trim();

            // Validação básica antes de enviar
            if (emailValue === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, preencha o campo de e-mail.',
                    confirmButtonColor: 'var(--helpdesk-primary)'
                });
                return;
            }

            // Desativa o botão e muda o texto para evitar duplo clique
            btnEnviar.disabled = true;
            const textoOriginal = btnEnviar.innerHTML;
            btnEnviar.innerHTML = 'Enviando...';

            // Monta os dados do formulário
            const formData = new FormData();
            formData.append('email', emailValue);

            // Dispara a requisição assíncrona para o PHP
            fetch('scripts/recuperar-senha.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message,
                        confirmButtonColor: 'var(--helpdesk-primary)'
                    }).then(() => {
                        // Limpa o formulário e fecha a modal utilizando o método do Bootstrap
                        formRecuperar.reset();
                        const modalElement = document.getElementById('modalRecuperarSenha');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: data.message,
                        confirmButtonColor: 'var(--helpdesk-primary)'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro no Sistema',
                    text: 'Não foi possível processar a requisição no momento.',
                    confirmButtonColor: 'var(--helpdesk-primary)'
                });
            })
            .finally(() => {
                // Restaura o botão após o término do processo
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = textoOriginal;
            });
        });
    }
});

})();
