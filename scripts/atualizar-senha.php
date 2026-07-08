<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Sobe um nível para achar a conexão na raiz
require_once __DIR__ . '/../conexao.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = filter_input(INPUT_POST, 'token', FILTER_UNSAFE_RAW);
    $nova_senha = filter_input(INPUT_POST, 'nova_senha', FILTER_UNSAFE_RAW);

    if (!$token || !$nova_senha || strlen($nova_senha) < 6) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos. A senha deve ter no mínimo 6 caracteres.']);
        exit;
    }

    // 1. Validar se o token existe, não foi usado e não expirou
    $stmt = $pdo->prepare("
        SELECT id, usuario_id FROM recuperacao_senha 
        WHERE token = :token AND usado = 0 AND expira_em > NOW() 
        LIMIT 1
    ");
    $stmt->execute([':token' => $token]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Este link de recuperação é inválido ou já expirou.']);
        exit;
    }

    // 2. Criptografar a nova senha
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        // 3. Atualizar a senha na tabela usuarios
        $upUser = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        $upUser->execute([
            ':senha' => $senha_hash,
            ':id'    => $pedido['usuario_id']
        ]);

        // 4. Marcar o token como usado para inutilizá-lo
        $upToken = $pdo->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE id = :id");
        $upToken->execute([':id' => $pedido['id']]);

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Senha atualizada com sucesso! Você já pode entrar do sistema.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar a senha. Tente novamente.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);