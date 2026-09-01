<?php
/**
 * painel/scripts/logs/relatorio.php
 * Gera o relatório de Logs em PDF (DomPDF), respeitando o mesmo filtro de data da tela
 * (painel/scripts/logs/filtro.php, compartilhado com listar.php).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id'])) {
    http_response_code(403);
    exit('Sessão expirada.');
}

require_once __DIR__ . '/../../../conexao.php';
require_once __DIR__ . '/filtro.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

[$whereSql, $params] = montarFiltroLogs($_GET);

$stmt = $pdo->prepare("
    SELECT l.acao, l.entidade, l.registro_id, l.descricao, l.ip, l.criado_em, u.nome AS usuario_nome
    FROM logs l
    LEFT JOIN usuarios u ON u.id = l.usuario_id
    {$whereSql}
    ORDER BY l.criado_em DESC
");
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dataInicial = $_GET['data_inicial'] ?? '';
$dataFinal   = $_GET['data_final'] ?? '';
$usuarioBusca = trim($_GET['usuario'] ?? '');

$periodo = ($dataInicial !== '' || $dataFinal !== '')
    ? sprintf(
        '%s até %s',
        $dataInicial !== '' ? date('d/m/Y', strtotime($dataInicial)) : 'início',
        $dataFinal !== '' ? date('d/m/Y', strtotime($dataFinal)) : 'hoje'
    )
    : 'Todos os registros';

if ($usuarioBusca !== '') {
    $periodo .= ' — Usuário: "' . $usuarioBusca . '"';
}

$acaoLabel = ['login' => 'Login', 'logout' => 'Logout', 'inserir' => 'Inserir', 'editar' => 'Editar', 'excluir' => 'Excluir'];

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; }
    h1 { font-size: 16px; margin-bottom: 2px; }
    .subtitulo { color: #64748b; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #cbd5e1; padding: 5px 7px; text-align: left; }
    th { background: #f1f5f9; }
    .badge { padding: 2px 6px; border-radius: 4px; color: #fff; font-size: 10px; }
    .b-login { background: #0d6efd; }
    .b-logout { background: #6c757d; }
    .b-inserir { background: #198754; }
    .b-editar { background: #e0a800; color: #000; }
    .b-excluir { background: #dc3545; }
    .rodape { margin-top: 16px; font-size: 9px; color: #94a3b8; }
</style>
</head>
<body>
    <h1>Relatório de Logs do Sistema</h1>
    <p class="subtitulo">Período: <?php echo htmlspecialchars($periodo); ?> — Gerado em <?php echo date('d/m/Y H:i'); ?></p>

    <table>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Usuário</th>
                <th>Ação</th>
                <th>Entidade</th>
                <th>Descrição</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
            <tr><td colspan="6" style="text-align:center; color:#94a3b8;">Nenhum registro encontrado nesse período.</td></tr>
            <?php else: foreach ($logs as $log): ?>
            <tr>
                <td><?php echo date('d/m/Y H:i:s', strtotime($log['criado_em'])); ?></td>
                <td><?php echo htmlspecialchars($log['usuario_nome'] ?: 'Sistema'); ?></td>
                <td><span class="badge b-<?php echo htmlspecialchars($log['acao']); ?>"><?php echo htmlspecialchars($acaoLabel[$log['acao']] ?? $log['acao']); ?></span></td>
                <td><?php echo htmlspecialchars($log['entidade']); ?></td>
                <td><?php echo htmlspecialchars($log['descricao'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($log['ip'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <p class="rodape">Total de registros: <?php echo count($logs); ?></p>
</body>
</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Numeração de página (ex.: "Página 1 de 3") em todas as páginas do PDF — {PAGE_NUM}/{PAGE_COUNT}
// são placeholders do próprio DomPDF, substituídos depois que ele já sabe quantas páginas existem.
$canvas = $dompdf->getCanvas();
$canvas->page_text(770, 570, 'Página {PAGE_NUM} de {PAGE_COUNT}', null, 8, [0.58, 0.64, 0.72]);

// Attachment=false -> abre no visualizador de PDF do próprio navegador (aba nova),
// em vez de forçar um download direto.
$dompdf->stream('relatorio-logs-' . date('Y-m-d_His') . '.pdf', ['Attachment' => false]);
