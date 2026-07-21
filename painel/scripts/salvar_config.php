<?php
@session_start();
header('Content-Type: application/json; charset=utf-8');

// Valida se o usuário está logado
if (empty($_SESSION['id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Sessão expirada.']);
    exit();
}

// Caminho relativo para a conexão dependendo de onde a pasta scripts está
require_once __DIR__ . '/../../conexao.php';

function resp($ok, $msg, $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resp(false, 'Método inválido.');
}

// Resgata os dados enviados pelo formulário
$nome_sistema = trim($_POST['nome_sistema'] ?? '');
$telefone_sistema = trim($_POST['telefone_sistema'] ?? '');
$email_sistema = trim($_POST['email_sistema'] ?? '');
$cor_primaria = trim($_POST['cor_primaria'] ?? '');
$cor_secundaria = trim($_POST['cor_secundaria'] ?? '');
$smtp_host = trim($_POST['smtp_host'] ?? '');
$smtp_porta = trim($_POST['smtp_porta'] ?? '');
$smtp_seguranca = trim($_POST['smtp_seguranca'] ?? '');
$smtp_senha = $_POST['smtp_senha'] ?? '';

try {
    if (!empty($smtp_senha)) {
        $sql = "UPDATE config SET 
                nome_sistema = :nome_sistema, 
                telefone_sistema = :telefone_sistema, 
                email_sistema = :email_sistema, 
                cor_primaria = :cor_primaria, 
                cor_secundaria = :cor_secundaria, 
                smtp_host = :smtp_host, 
                smtp_porta = :smtp_porta, 
                smtp_seguranca = :smtp_seguranca,
                smtp_senha = :smtp_senha
                WHERE empresa = 0 LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome_sistema' => $nome_sistema, 
            ':telefone_sistema' => $telefone_sistema, 
            ':email_sistema' => $email_sistema, 
            ':cor_primaria' => $cor_primaria, 
            ':cor_secundaria' => $cor_secundaria, 
            ':smtp_host' => $smtp_host, 
            ':smtp_porta' => $smtp_porta, 
            ':smtp_seguranca' => $smtp_seguranca, 
            ':smtp_senha' => $smtp_senha
        ]);
    } else {
        $sql = "UPDATE config SET 
                nome_sistema = :nome_sistema, 
                telefone_sistema = :telefone_sistema, 
                email_sistema = :email_sistema, 
                cor_primaria = :cor_primaria, 
                cor_secundaria = :cor_secundaria, 
                smtp_host = :smtp_host, 
                smtp_porta = :smtp_porta,
                smtp_seguranca = :smtp_seguranca
                WHERE empresa = 0 LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome_sistema' => $nome_sistema, 
            ':telefone_sistema' => $telefone_sistema, 
            ':email_sistema' => $email_sistema, 
            ':cor_primaria' => $cor_primaria, 
            ':cor_secundaria' => $cor_secundaria, 
            ':smtp_host' => $smtp_host, 
            ':smtp_porta' => $smtp_porta, 
            ':smtp_seguranca' => $smtp_seguranca
        ]);
    }

    // Atualiza o cache de config em sessão (conexao.php lê a config de $_SESSION['config']
    // e só volta ao banco quando esse cache não existe). Sem isso, o modal continuaria
    // mostrando os valores antigos da sessão mesmo após salvar corretamente no banco.
    $stmtRecarrega = $pdo->query("SELECT * FROM config WHERE empresa = 0 LIMIT 1");
    $_SESSION['config'] = $stmtRecarrega->fetch() ?: [];
    $_SESSION['config_empresa'] = 0;

    resp(true, 'Configurações salvas com sucesso!');

} catch (PDOException $e) {
    resp(false, 'Erro ao salvar no banco de dados: ' . $e->getMessage());
}