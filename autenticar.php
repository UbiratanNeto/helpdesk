<?php
/**
 * Autenticação de login - valida usuário/senha e inicia sessão
 */

// Inicia sessão e grava dados do usuário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexao.php';

// Só processa se for POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';


if ($username === '' || $password === '') {
    header('Location: index.php?erro=1');
    exit;
}

// Busca usuário por email (campo "Usuário" pode ser o email)
$stmt = $pdo->prepare("SELECT id, nome, email, senha, nivel, ativo, empresa FROM usuarios WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $username]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['senha'])) {
    header('Location: index.php?erro=1');
    exit;
}

// Verifica se está ativo
if (empty($usuario['ativo']) || $usuario['ativo'] === '0') {
    header('Location: index.php?erro=inativo');
    exit;
}


$_SESSION['id']    = (int) $usuario['id'];
$_SESSION['nome']  = $usuario['nome'];
$_SESSION['email'] = $usuario['email'];
$_SESSION['nivel'] = $usuario['nivel'];
$_SESSION['id_empresa'] = (int) $usuario['empresa'];

// Redireciona para a área logada (criar dashboard.php depois se quiser)
header('Location: painel');
exit;
