/**
 * painel/assets/js/ajax-forms.js
 * Intercepta os formulários das modais e exibe o SweetAlert2 usando o objeto global Mensagens
 */
document.addEventListener('DOMContentLoaded', () => {

    function configurarFormularioAjax(formSelector, modalSelector) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Impede o envio padrão

            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');

            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    // Utiliza a função global de sucesso do seu js/mensagens.js
                    Mensagens.sucesso('Sucesso!', data.msg).then(() => {
                        // Fecha a modal correspondente
                        const modal = document.querySelector(modalSelector);
                        if (modal) modal.style.display = 'none';

                        // Recarrega a página para refletir as alterações
                        location.reload();
                    });
                } else {
                    // Utiliza a função global de erro do seu js/mensagens.js
                    Mensagens.erro('Atenção', data.msg);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                Mensagens.erro('Erro', 'Ocorreu um erro ao tentar salvar. Verifique o console.');
            });
        });
    }

    // Ativa para a modal de Configurações
    configurarFormularioAjax('form[action="scripts/salvar_config.php"]', '#modalConfig');

    // Ativa para a modal de Perfil
    configurarFormularioAjax('form[action="scripts/salvar_perfil.php"]', '#modalPerfil');
});