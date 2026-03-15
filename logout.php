<?php
/**
 * Logout - encerra a sessão e remove o cookie de sessão
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis da sessão
$_SESSION = [];

// Obtém parâmetros do cookie de sessão para removê-lo corretamente
$params = session_get_cookie_params();

// Remove o cookie de sessão (expira no passado)
setcookie(
    session_name(),
    '',
    time() - 42000,
    $params['path'],
    $params['domain'],
    $params['secure'],
    $params['httponly']
);

// Destrói a sessão
session_destroy();

header('Location: index.php');
exit;
