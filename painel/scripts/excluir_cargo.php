<?php
/**
 * painel/scripts/excluir_cargo.php
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

require_once __DIR__ . '/../../conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    resp(false, 'ID inválido.');
}

try {
    $stmt = $pdo->prepare("DELETE FROM cargos WHERE id = ?");
    $stmt->execute([$id]);

    resp(true, 'Cargo removido com sucesso!');
} catch (PDOException $e) {
    resp(false, 'Erro ao excluir cargo: ' . $e->getMessage());
}
