<?php
/**
 * painel/scripts/salvar_cargo.php
 * Cria ou atualiza um cargo (Gestão de Cargos).
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resp(false, 'Método inválido.');
}

require_once __DIR__ . '/../../conexao.php';

$id   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome'] ?? '');

if ($nome === '') {
    resp(false, 'Informe o nome do cargo.');
}

try {
    // Nome duplicado (considerando outros cargos, não o próprio ao editar)
    $stmtDup = $pdo->prepare("SELECT id FROM cargos WHERE nome = ? AND id != ?");
    $stmtDup->execute([$nome, $id ?? 0]);
    if ($stmtDup->fetch()) {
        resp(false, 'Já existe um cargo com esse nome.');
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE cargos SET nome = :nome WHERE id = :id");
        $stmt->execute([':nome' => $nome, ':id' => $id]);
        resp(true, 'Cargo atualizado com sucesso!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO cargos (nome, empresa) VALUES (:nome, :empresa)");
        $stmt->execute([':nome' => $nome, ':empresa' => $_SESSION['id_empresa'] ?? 0]);
        resp(true, 'Cargo cadastrado com sucesso!');
    }
} catch (PDOException $e) {
    resp(false, 'Erro no banco de dados: ' . $e->getMessage());
}
