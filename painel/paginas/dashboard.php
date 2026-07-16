<?php
/**
 * painel/paginas/dashboard.php
 * Conteúdo interno do Dashboard. Carregado dinamicamente via painel/index.php
 */

// Validação extra para garantir que o banco e a sessão estejam disponíveis
if (!isset($pdo) || !isset($_SESSION['id_empresa'])) {
    die("Acesso restrito ou sessão expirada.");
}

try {
    // 1. Query para buscar métricas reais baseadas no ID da empresa salvo na sessão
    // Chamados Abertos (Status = 'Aberto')
    $stmtOpen = $pdo->prepare("SELECT COUNT(id) FROM chamados WHERE empresa_id = :empresa AND status = 'Aberto'");
    $stmtOpen->execute([':empresa' => $_SESSION['id_empresa']]);
    $total_abertos = $stmtOpen->fetchColumn();

    // Chamados Fechados (Status = 'Fechado')
    $stmtClosed = $pdo->prepare("SELECT COUNT(id) FROM chamados WHERE empresa_id = :empresa AND status = 'Fechado'");
    $stmtClosed->execute([':empresa' => $_SESSION['id_empresa']]);
    $total_fechados = $stmtClosed->fetchColumn();

    // Clientes Registrados na empresa
    $stmtClientes = $pdo->prepare("SELECT COUNT(id) FROM clientes WHERE empresa_id = :empresa");
    $stmtClientes->execute([':empresa' => $_SESSION['id_empresa']]);
    $total_clientes = $stmtClientes->fetchColumn();

    // Usuários ativos da empresa
    $stmtUsers = $pdo->prepare("SELECT COUNT(id) FROM usuarios WHERE empresa = :empresa AND ativo = 1");
    $stmtUsers->execute([':empresa' => $_SESSION['id_empresa']]);
    $total_usuarios = $stmtUsers->fetchColumn();

    // 2. Query para listar os últimos 4 chamados atualizados
    $stmtChamados = $pdo->prepare("
        SELECT id, assunto, cliente_nome, status 
        FROM chamados 
        WHERE empresa_id = :empresa 
        ORDER BY data_atualizacao DESC 
        LIMIT 4
    ");
    $stmtChamados->execute([':empresa' => $_SESSION['id_empresa']]);
    $ultimos_chamados = $stmtChamados->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Valores de contingência caso as tabelas ainda estejam sendo estruturadas
    $total_abertos = 0;
    $total_fechados = 0;
    $total_clientes = 0;
    $total_usuarios = 0;
    $ultimos_chamados = [];
}
?>

<!-- CONTEÚDO DO DASHBOARD -->
<div class="hd-welcome">
    <h1 class="hd-welcome__title">Olá, <?php echo htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
</div>

<!-- GRIDS DE CARTÕES DE MÉTRICA -->
<section class="hd-metrics-grid">
    <div class="hd-card hd-metric">
        <div class="hd-card__body hd-metric__info">
            <h3>Chamados Abertos</h3>
            <p class="hd-metric__value"><?php echo (int)$total_abertos; ?></p>
        </div>
        <div class="hd-card__body">
            <div class="hd-metric__icon-box hd-metric__icon-box--blue">
                <i class="fa-solid fa-envelope-open"></i>
            </div>
        </div>
    </div>

    <div class="hd-card hd-metric">
        <div class="hd-card__body hd-metric__info">
            <h3>Chamados Fechados</h3>
            <p class="hd-metric__value"><?php echo (int)$total_fechados; ?></p>
        </div>
        <div class="hd-card__body">
            <div class="hd-metric__icon-box hd-metric__icon-box--green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <div class="hd-card hd-metric">
        <div class="hd-card__body hd-metric__info">
            <h3>Clientes Reg.</h3>
            <p class="hd-metric__value"><?php echo (int)$total_clientes; ?></p>
        </div>
        <div class="hd-card__body">
            <div class="hd-metric__icon-box hd-metric__icon-box--orange">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <div class="hd-card hd-metric">
        <div class="hd-card__body hd-metric__info">
            <h3>Usuários Ativos</h3>
            <p class="hd-metric__value"><?php echo (int)$total_usuarios; ?></p>
        </div>
        <div class="hd-card__body">
            <div class="hd-metric__icon-box hd-metric__icon-box--red">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
    </div>
</section>

<!-- VISÃO GERAL (GRÁFICO + TABELA) -->
<section class="hd-details-grid">
    
    <!-- Bloco do Gráfico -->
    <div class="hd-card">
        <div class="hd-card__header">
            <h2 class="hd-card__title" style="font-size: 1.15rem;">Chamados por Mês</h2>
        </div>
        <div class="hd-card__body" style="height: 280px; position: relative;">
            <canvas id="canvasChart"></canvas>
        </div>
    </div>

    <!-- Bloco da Tabela -->
    <div class="hd-card">
        <div class="hd-card__header hd-card__header--row">
            <div>
                <h2 class="hd-card__title" style="font-size: 1.15rem;">Últimos Chamados</h2>
            </div>
            <a href="index.php?p=chamados" class="hd-link">Ver todos</a>
        </div>
        <div class="hd-card__body">
            <div class="hd-table-responsive">
                <table class="hd-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Assunto</th>
                            <th>Cliente</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ultimos_chamados)): ?>
                            <?php foreach ($ultimos_chamados as $index => $chamado): ?>
                                <?php 
                                    $assunto = htmlspecialchars($chamado['assunto'], ENT_QUOTES, 'UTF-8');
                                    $cliente = htmlspecialchars($chamado['cliente_nome'], ENT_QUOTES, 'UTF-8');
                                    $status  = strtolower($chamado['status']);
                                    
                                    $badge_class = 'hd-badge--pending';
                                    if ($status === 'aberto') $badge_class = 'hd-badge--open';
                                    if ($status === 'fechado' || $status === 'resolvido') $badge_class = 'hd-badge--closed';
                                ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo $assunto; ?></strong></td>
                                    <td><?php echo $cliente; ?></td>
                                    <td><span class="hd-badge <?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback estático de amostragem amigável -->
                            <tr>
                                <td>1</td>
                                <td><strong>Problema no servidor</strong></td>
                                <td>João Souza</td>
                                <td><span class="hd-badge hd-badge--open">Aberto</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>Atualização de Sistema</strong></td>
                                <td>Ana Lima</td>
                                <td><span class="hd-badge hd-badge--pending">Pendente</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><strong>Erro de login</strong></td>
                                <td>Patrícia Melo</td>
                                <td><span class="hd-badge hd-badge--closed">Fechado</span></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>

<!-- Script de Inicialização do Gráfico do Dashboard -->
<script>
    (function() {
        const ctx = document.getElementById('canvasChart').getContext('2d');
        const rootStyle = getComputedStyle(document.documentElement);
        const primaryColor = rootStyle.getPropertyValue('--cor-primaria').trim() || '#4f46e5';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Chamados',
                    data: [4.5, 6, 5.2, 7.5, 5.8, 8.5],
                    borderColor: primaryColor,
                    backgroundColor: primaryColor + '1A', 
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: primaryColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    })();
</script>