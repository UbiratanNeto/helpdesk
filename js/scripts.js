// js/scripts.js

document.addEventListener('DOMContentLoaded', function () {
    const btnEnviar = document.getElementById('btnEnviarRecuperacao');
    const formRecuperar = document.getElementById('formRecuperarSenha');
    const inputEmail = document.getElementById('emailRecuperar');

    if (btnEnviar) {
        btnEnviar.addEventListener('click', function (e) {
            e.preventDefault();

            const emailValue = inputEmail.value.trim();

            if (emailValue === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, preencha o campo de e-mail.',
                    confirmButtonColor: 'var(--helpdesk-primary)'
                });
                return;
            }

            btnEnviar.disabled = true;
            const textoOriginal = btnEnviar.innerHTML;
            btnEnviar.innerHTML = 'Enviando...';

            const formData = new FormData();
            formData.append('email', emailValue);

            // Aponta corretamente para a pasta scripts onde está o PHP
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
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = textoOriginal;
            });
        });
    }
});