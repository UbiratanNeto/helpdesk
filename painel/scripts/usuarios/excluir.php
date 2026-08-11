<?php
/**
 * painel/scripts/usuarios/excluir.php
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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    resp(false, 'ID inválido.');
}

try {
    // Buscar e apagar foto antiga se existir (exceto o fallback padrão do sistema)
    $stmt = $pdo->prepare("SELECT foto FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();

    if ($usuario && !empty($usuario['foto']) && $usuario['foto'] !== 'sem_foto.png') {
        $fotoPath = __DIR__ . '/../../../uploads/perfil/' . $usuario['foto'];
        if (is_file($fotoPath)) {
            @unlink($fotoPath);
        }
    }

    $stmtDelete = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmtDelete->execute([$id]);

    resp(true, 'Usuário removido com sucesso!');
} catch (PDOException $e) {
    resp(false, 'Erro ao excluir usuário: ' . $e->getMessage());
}
