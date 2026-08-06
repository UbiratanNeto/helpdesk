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

// Mesma ideia do menuAtivo(), mas pra grupos com submenu: destaca o cabeçalho
// do grupo (ex.: "Cadastros") quando a página atual é uma das filhas dele.
function grupoAtivo(array $paginasFilhas, $atual) {
    return in_array($atual, $paginasFilhas, true) ? 'hd-sidebar__group--active' : '';
}

// Grupo "Cadastros": lista de páginas-filhas usada tanto pro destaque quanto
// pra decidir se o submenu já nasce aberto (quando você está numa delas).
$cadastrosPages = ['categorias', 'setores', 'cargos'];
$cadastrosOpen  = in_array($atual, $cadastrosPages, true);
?>
<!-- Sidebar (.hd-layout__sidebar) -->
<aside class="hd-layout__sidebar">
    <div>
        <div class="hd-sidebar__brand">
            <div class="hd-sidebar__brand-icon" aria-hidden="true">
                <i class="fa-solid fa-headset"></i>
            </div>
            <span><?php echo htmlspecialchars($nome_sistema ?? 'Helpdesk', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        
        <ul class="hd-sidebar__menu">
            <!-- Item: Dashboard -->
            <li class="hd-sidebar__item <?php echo menuAtivo('dashboard', $atual); ?>">
                <a href="dashboard" class="hd-sidebar__link">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Item: Cadastros (grupo com submenu — mesmo padrão data-submenu/data-submenu-content
                 reaproveitável pra qualquer outro grupo futuro, sem depender de Bootstrap) -->
            <li class="hd-sidebar__item hd-sidebar__item--group <?php echo grupoAtivo($cadastrosPages, $atual); ?>">
                <button type="button"
                        class="hd-sidebar__link hd-sidebar__group-toggle"
                        data-submenu="cadastros"
                        aria-expanded="<?php echo $cadastrosOpen ? 'true' : 'false'; ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Cadastros</span>
                    <i class="fa-solid fa-chevron-down hd-sidebar__chevron" aria-hidden="true"></i>
                </button>

                <ul class="hd-sidebar__submenu" data-submenu-content="cadastros" <?php echo $cadastrosOpen ? '' : 'hidden'; ?>>
                    <li class="hd-sidebar__subitem <?php echo menuAtivo('categorias', $atual); ?>">
                        <a href="categorias" class="hd-sidebar__sublink">
                            <i class="fa-solid fa-tags"></i>
                            <span>Categorias</span>
                        </a>
                    </li>
                    <li class="hd-sidebar__subitem <?php echo menuAtivo('setores', $atual); ?>">
                        <a href="setores" class="hd-sidebar__sublink">
                            <i class="fa-solid fa-sitemap"></i>
                            <span>Setores</span>
                        </a>
                    </li>
                    <li class="hd-sidebar__subitem <?php echo menuAtivo('cargos', $atual); ?>">
                        <a href="cargos" class="hd-sidebar__sublink">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>Cargos</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Item: Chamados -->
            <li class="hd-sidebar__item <?php echo menuAtivo('chamados', $atual); ?>">
                <a href="chamados" class="hd-sidebar__link">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Chamados</span>
                </a>
            </li>
            
            <!-- Item: Clientes -->
            <li class="hd-sidebar__item <?php echo menuAtivo('clientes', $atual); ?>">
                <a href="clientes" class="hd-sidebar__link">
                    <i class="fa-solid fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            
            <!-- Item: Usuários -->
            <li class="hd-sidebar__item <?php echo menuAtivo('usuarios', $atual); ?>">
                <a href="usuarios" class="hd-sidebar__link">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Usuários</span>
                </a>
            </li>

            <!-- Item: Relatórios -->
            <li class="hd-sidebar__item <?php echo menuAtivo('relatorios', $atual); ?>">
                <a href="relatorios" class="hd-sidebar__link">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Relatórios</span>
                </a>
            </li>

            <!-- Item: Configurações (Apenas Administradores) -->
            <?php if ($nivel_usuario_atual === 'admin' || $nivel_usuario_atual === 'administrador'): ?>
            <li class="hd-sidebar__item <?php echo menuAtivo('configuracoes', $atual); ?>">
                <a href="configuracoes" class="hd-sidebar__link">
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