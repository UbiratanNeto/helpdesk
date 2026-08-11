<?php
/**
 * painel/scripts/cargos/buscar.php
 * Devolve os dados de um cargo, incluindo acesso_total e a lista de menus permitidos —
 * usado pra pré-preencher o modal de edição (nome + checklist de permissões).
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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    resp(false, 'ID inválido.');
}

$stmt = $pdo->prepare("SELECT id, nome, acesso_total FROM cargos WHERE id = ?");
$stmt->execute([$id]);
$cargo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cargo) {
    resp(false, 'Cargo não encontrado.');
}

$cargo['acesso_total'] = (int) $cargo['acesso_total'];

$stmtPerm = $pdo->prepare("SELECT menu FROM cargo_permissoes WHERE cargo_id = ?");
$stmtPerm->execute([$id]);
$cargo['permissoes'] = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);

resp(true, '', ['data' => $cargo]);
