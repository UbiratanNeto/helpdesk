/**
 * painel/assets/js/functions.js
 * Funções utilitárias e integração com ViaCEP
 */
document.addEventListener('DOMContentLoaded', () => {
    const inputCep = document.getElementById('perfil_cep');

    if (inputCep) {
        inputCep.addEventListener('blur', function() {
            let cep = this.value.replace(/\D/g, '');

            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            atribuirValor('perfil_endereco', data.logradouro);
                            atribuirValor('perfil_bairro', data.bairro);
                            atribuirValor('perfil_cidade', data.localidade);
                            atribuirValor('perfil_estado', data.uf);
                            
                            let inputNumero = document.getElementById('perfil_numero');
                            if (inputNumero) inputNumero.focus();
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                    .catch(error => console.error('Erro ao buscar o CEP:', error));
            }
        });
    }
});

function atribuirValor(id, valor) {
    const campo = document.getElementById(id);
    if (campo) {
        campo.value = valor;
    }
}