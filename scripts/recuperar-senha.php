<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../conexao.php';

// Define o retorno estrito como JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Por favor, insira um e-mail válido.']);
        exit;
    }

    // Verifica se o usuário existe no banco de dados
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = :email AND ativo = '1' LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // ===== LOGICA DE ENVIO DE E-MAIL FUTURA =====
        // Aqui futuramente você integrará o PHPMailer ou a função mail() usando os dados que o Hugo ensina no curso.
        // Por enquanto, simulamos o sucesso para testar o comportamento visual da modal.
        
        echo json_encode([
            'success' => true, 
            'message' => 'Instruções enviadas com sucesso! Verifique sua caixa de entrada.'
        ]);
    } else {
        // Por segurança, você pode optar por dizer que enviou mesmo que não ache, 
        // ou avisar que o e-mail não está cadastrado. Vamos avisar aqui:
        echo json_encode(['success' => false, 'message' => 'Este e-mail não foi encontrado em nosso sistema ou a conta está inativa.']);
    }
    exit;
}

// Se tentarem acessar o arquivo diretamente
echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);