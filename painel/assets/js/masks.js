/**
 * painel/assets/js/masks.js
 * Gerencia as máscaras de input usando a biblioteca IMask
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof IMask !== 'undefined') {
        
        // Máscara para CPF (000.000.000-00)
        const cpfInput = document.getElementById('perfil_cpf');
        if (cpfInput) IMask(cpfInput, { mask: '000.000.000-00' });

        // Máscara para CEP (00000-000) - Funciona no modal de perfil
        const cepInput = document.getElementById('perfil_cep');
        if (cepInput) IMask(cepInput, { mask: '00000-000' });

        // Máscara para Telefones (Sistema e Perfil)
        const telPerfil = document.getElementById('perfil_telefone');
        if (telPerfil) {
            IMask(telPerfil, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }] });
        }

        const telSistema = document.getElementById('telefone_sistema');
        if (telSistema) {
            IMask(telSistema, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }] });
        }
    }
});