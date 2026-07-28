/**
 * painel/assets/js/functions.js
 * Funções utilitárias e integração com ViaCEP
 */
document.addEventListener('DOMContentLoaded', () => {
    // Cada grupo é um formulário diferente que tem os mesmos 6 campos de endereço.
    // Pra adicionar autofill de CEP em um formulário novo, basta acrescentar um grupo aqui.
    const grupos = [
        { cep: 'perfil_cep', endereco: 'perfil_endereco', bairro: 'perfil_bairro', cidade: 'perfil_cidade', estado: 'perfil_estado', numero: 'perfil_numero' },
        { cep: 'usuario_cep', endereco: 'usuario_endereco', bairro: 'usuario_bairro', cidade: 'usuario_cidade', estado: 'usuario_estado', numero: 'usuario_numero' },
    ];

    grupos.forEach(function (campos) {
        const inputCep = document.getElementById(campos.cep);
        if (!inputCep) return;

        inputCep.addEventListener('blur', function() {
            let cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.erro) {
                        atribuirValor(campos.endereco, data.logradouro);
                        atribuirValor(campos.bairro, data.bairro);
                        atribuirValor(campos.cidade, data.localidade);
                        atribuirValor(campos.estado, data.uf);

                        let inputNumero = document.getElementById(campos.numero);
                        if (inputNumero) inputNumero.focus();
                    } else {
                        alert('CEP não encontrado.');
                    }
                })
                .catch(function (error) { console.error('Erro ao buscar o CEP:', error); });
        });
    });
});

function atribuirValor(id, valor) {
    const campo = document.getElementById(id);
    if (campo) {
        campo.value = valor;
    }
}