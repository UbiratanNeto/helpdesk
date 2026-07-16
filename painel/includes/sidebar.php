<?php
/**
 * painel/includes/sidebar.php
 * Menu lateral dinâmico
 */

// Define qual a página atual baseado no parâmetro 'p' da URL (ex: index.php?p=dashboard)
// Se não houver nenhum parâmetro, define 'dashboard' como padrão
$atual = $_GET['p'] ?? 'dashboard';

// Recupera o nível de acesso direto da sessão global de login de forma segura
$nivel_usuario_atual = strtolower($_SESSION['nivel'] ?? $_SESSION['cargo'] ?? '');

// Função idêntica à do seu curso para ativar o menu correto
function menuAtivo($pagina, $atual) {
    return $pagina === $atual ? 'hd-sidebar__item--active' : '';
}
?>
<!-- Sidebar (.hd-layout__sidebar) -->
<aside class="hd-layout__sidebar">
    <div>
        <div class="hd-sidebar__brand">
            <i class="fa-solid fa-headset"></i>
            <span><?php echo htmlspecialchars($nome_sistema ?? 'Helpdesk', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        
        <ul class="hd-sidebar__menu">
            <!-- Item: Dashboard -->
            <li class="hd-sidebar__item <?php echo menuAtivo('dashboard', $atual); ?>">
                <a href="index.php?p=dashboard" class="hd-sidebar__link">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Item: Chamados -->
            <li class="hd-sidebar__item <?php echo menuAtivo('chamados', $atual); ?>">
                <a href="index.php?p=chamados" class="hd-sidebar__link">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Chamados</span>
                </a>
            </li>
            
            <!-- Item: Clientes -->
            <li class="hd-sidebar__item <?php echo menuAtivo('clientes', $atual); ?>">
                <a href="index.php?p=clientes" class="hd-sidebar__link">
                    <i class="fa-solid fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            
            <!-- Item: Configurações (Apenas Administradores) -->
            <?php if ($nivel_usuario_atual === 'admin' || $nivel_usuario_atual === 'administrador'): ?>
            <li class="hd-sidebar__item <?php echo menuAtivo('configuracoes', $atual); ?>">
                <a href="index.php?p=configuracoes" class="hd-sidebar__link">
                    <i class="fa-solid fa-gears"></i>
                    <span>Configurações</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="hd-sidebar__footer">
        <!-- O logout.php está na raiz, então usamos ../logout.php -->
        <a href="../logout.php" class="hd-sidebar__footer-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Sair do Sistema</span>
        </a>
    </div>
</aside>