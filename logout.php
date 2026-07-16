<?php
/**
 * logout.php
 * Encerra a sessão de forma segura, limpa cookies de identificação e redireciona para o login
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Limpa todas as variáveis de sessão em memória
$_SESSION = [];

// 2. Destrói o cookie de sessão do navegador (expira no passado)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destrói o cookie de "Lembrar-me" / Autologin (caso seu sistema utilize)
if (isset($_COOKIE['lembrar_usuario'])) {
    setcookie('lembrar_usuario', '', time() - 3600, '/');
}

// 4. Destrói fisicamente a sessão no servidor
session_destroy();

// 5. Redireciona de forma limpa para a tela de login na raiz
header('Location: index.php');
exit;