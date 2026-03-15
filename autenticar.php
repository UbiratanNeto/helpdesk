<?php
/**
 * Autenticação de login - valida usuário/senha e inicia sessão
 * Medidas: CSRF, validação de entrada, limite de tentativas, regeneração de sessão
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/conexao.php';

// ---------- Só processa POST ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// ---------- Proteção CSRF ----------
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token_login'] ?? '', $token)) {
    $_SESSION['login_mensagem'] = '1';
    header('Location: index.php');
    exit;
}
// Usa o token uma vez (opcional: regenerar na próxima exibição do form)
unset($_SESSION['csrf_token_login']);

// ---------- Limite de tentativas (proteção contra força bruta) ----------
$max_tentativas = 5;
$bloqueio_minutos = 15;
if (empty($_SESSION['login_tentativas'])) {
    $_SESSION['login_tentativas'] = 0;
    $_SESSION['login_ultima_tentativa'] = time();
}
if ($_SESSION['login_tentativas'] >= $max_tentativas) {
    $passou = (time() - $_SESSION['login_ultima_tentativa']) > ($bloqueio_minutos * 60);
    if (!$passou) {
        $_SESSION['login_mensagem'] = 'bloqueado';
        header('Location: index.php');
        exit;
    }
    $_SESSION['login_tentativas'] = 0;
}

// ---------- Entrada: sanitizar e validar ----------
$username = str_replace(["\0", "\r", "\n"], '', trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

// Tamanhos máximos (evita payload gigante)
$username = mb_substr($username, 0, 100, 'UTF-8');
if (strlen($password) > 255) {
    $password = '';
}

if ($username === '' || $password === '') {
    $_SESSION['login_tentativas'] = ($_SESSION['login_tentativas'] ?? 0) + 1;
    $_SESSION['login_ultima_tentativa'] = time();
    $_SESSION['login_mensagem'] = '1';
    header('Location: index.php');
    exit;
}

// Login por e-mail: validar formato
if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_tentativas'] = ($_SESSION['login_tentativas'] ?? 0) + 1;
    $_SESSION['login_ultima_tentativa'] = time();
    $_SESSION['login_mensagem'] = '1';
    header('Location: index.php');
    exit;
}

// ---------- SQL com prepared statement (proteção contra SQL Injection) ----------
$stmt = $pdo->prepare("SELECT id, nome, email, senha, nivel, ativo, empresa FROM usuarios WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $username]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['senha'])) {
    $_SESSION['login_tentativas'] = ($_SESSION['login_tentativas'] ?? 0) + 1;
    $_SESSION['login_ultima_tentativa'] = time();
    $_SESSION['login_mensagem'] = '1';
    header('Location: index.php');
    exit;
}

// Verifica se está ativo
if (empty($usuario['ativo']) || $usuario['ativo'] === '0') {
    $_SESSION['login_mensagem'] = 'inativo';
    header('Location: index.php');
    exit;
}

// Sucesso: zera tentativas e regenera ID da sessão (evita session fixation)
$_SESSION['login_tentativas'] = 0;
session_regenerate_id(true);

$_SESSION['id']         = (int) $usuario['id'];
$_SESSION['nome']       = $usuario['nome'];
$_SESSION['email']      = $usuario['email'];
$_SESSION['nivel']      = $usuario['nivel'];
$_SESSION['id_empresa'] = (int) $usuario['empresa'];

header('Location: painel');
exit;
