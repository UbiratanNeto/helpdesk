/**
 * painel/assets/js/masks.js
 * Gerencia as máscaras de input usando a biblioteca IMask
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof IMask === 'undefined') return;

    // Máscara para CPF (000.000.000-00) — reaproveitada em todo formulário que tiver esses IDs
    ['perfil_cpf', 'usuario_cpf'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) IMask(el, { mask: '000.000.000-00' });
    });

    // Máscara para CEP (00000-000)
    ['perfil_cep', 'usuario_cep'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) IMask(el, { mask: '00000-000' });
    });

    // Máscara para Telefones (fixo ou celular)
    ['perfil_telefone', 'telefone_sistema', 'usuario_telefone'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) IMask(el, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }] });
    });
});