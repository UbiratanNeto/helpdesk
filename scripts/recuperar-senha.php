<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Sobe um nível para achar a conexão na raiz
require_once __DIR__ . '/../conexao.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Por favor, insira um e-mail válido.']);
        exit;
    }

    // 1. Verifica se o usuário existe e está ativo
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = :email AND ativo = '1' LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // 2. Gerar Token seguro
        $token = bin2hex(random_bytes(32));
        
        // 3. Validade de 2 horas
        $expira_em = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        // 4. Salvar na tabela de recuperação
        $ins = $pdo->prepare("
            INSERT INTO recuperacao_senha (usuario_id, token, expira_em, usado) 
            VALUES (:usuario_id, :token, :expira_em, 0)
        ");
        
        $ins->execute([
            ':usuario_id' => $usuario['id'],
            ':token'      => $token,
            ':expira_em'  => $expira_em
        ]);

        // 5. Link apontando para a página visual na raiz
        $link_recuperacao = "http://localhost/helpdesk/redefinir-senha.php?token=" . $token;

        // ===== ENVIO DE E-MAIL (PHPMailer / mail) =====
        // Use a variável $link_recuperacao no corpo do seu e-mail aqui.
        // ===============================================

        echo json_encode([
            'success' => true, 
            'message' => 'Instruções enviadas com sucesso! Verifique sua caixa de entrada.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Este e-mail não foi encontrado em nosso sistema ou a conta está inativa.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);