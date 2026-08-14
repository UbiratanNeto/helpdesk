<?php
/**
 * painel/scripts/clientes/excluir.php
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

function resp($ok, $msg, $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra), JSON_UNESCAPED_UNICODE);
    exit();
}

if (empty($_SESSION['id'])) {
    resp(false, 'Sessão expirada.');
}

require_once __DIR__ . '/../../../conexao.php';
require_once __DIR__ . '/../../includes/permissoes.php';

if (!podeExecutarAcao($pdo, $_SESSION['id'] ?? null, $_SESSION['cargo_id'] ?? null, 'excluir')) {
    resp(false, 'Você não tem permissão para excluir clientes.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    resp(false, 'ID inválido.');
}

try {
    // Buscar e apagar foto antiga se existir (exceto o fallback padrão do sistema)
    $stmt = $pdo->prepare("SELECT foto FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();

    if ($cliente && !empty($cliente['foto']) && $cliente['foto'] !== 'sem_foto.png') {
        $fotoPath = __DIR__ . '/../../../uploads/clientes/' . $cliente['foto'];
        if (is_file($fotoPath)) {
            @unlink($fotoPath);
        }
    }

    $stmtDelete = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    $stmtDelete->execute([$id]);

    resp(true, 'Cliente removido com sucesso!');
} catch (PDOException $e) {
    resp(false, 'Erro ao excluir cliente: ' . $e->getMessage());
}
