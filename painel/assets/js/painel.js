/**
 * painel/assets/js/painel.js
 * Gerencia a alternância da Sidebar e persistência no localStorage
 */

document.addEventListener('DOMContentLoaded', () => {
    // Seleciona o container principal que engloba o layout
    // No seu CSS atual, o layout usa a classe .hd-layout
    const app = document.querySelector('.hd-layout');
    
    // Seleciona o botão de toggle (ID definido no seu topbar.php)
    const btnToggle = document.getElementById('btn-toggle-sidebar');

    if (btnToggle && app) {
        btnToggle.addEventListener('click', () => {
            // Alterna a classe de estado no container principal
            // O seu CSS usa .hd-layout--sidebar-collapsed
            app.classList.toggle('hd-layout--sidebar-collapsed');
            
            // Salva o estado no localStorage para manter a preferência do usuário
            const isCollapsed = app.classList.contains('hd-layout--sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed ? '1' : '0');
        });

        // Verifica o estado salvo anteriormente ao carregar a página
        if (localStorage.getItem('sidebar-collapsed') === '1') {
            app.classList.add('hd-layout--sidebar-collapsed');
        }
    }
});