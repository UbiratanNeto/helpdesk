<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexao.php';

// Token CSRF para o formulário de login
if (empty($_SESSION['csrf_token_login'])) {
    $_SESSION['csrf_token_login'] = bin2hex(random_bytes(32));
}

// Mensagem de erro/situação de login via sessão (flash)
$loginMensagem = $_SESSION['login_mensagem'] ?? null;
if ($loginMensagem !== null) {
    unset($_SESSION['login_mensagem']);
}

// ===== USUÁRIO ADMINISTRADOR PADRÃO (criado só se ainda não existir) =====
$stmt = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE nivel = 'Administrador'");
if ($stmt->fetchColumn() == 0) {
    $senha_hash = password_hash($senha_padrao, PASSWORD_DEFAULT);
    $ins = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, nivel, ativo, empresa)
        VALUES (:nome, :email, :senha, 'Administrador', :ativo, :empresa)
    ");
    $ins->execute([
        ':nome'   => 'Administrador',
        ':email'  => $email_sistema,
        ':senha'  => $senha_hash,
        ':ativo'  => '1',
        ':empresa'=> $id_empresa,
    ]);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars($nome_sistema); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Cores do tema (variáveis do sistema) -->
    <style>
    :root {
        --helpdesk-primary: <?php echo $cor_primaria; ?>;
        --helpdesk-primary-dark: <?php echo $cor_primaria; ?>;
        --helpdesk-bg: <?php echo $cor_fundo; ?>;
    }
    </style>
    <?php if ($loginMensagem !== null): ?>
    <script>
        window.LOGIN_MENSAGEM = <?php echo json_encode($loginMensagem, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    </script>
    <?php endif; ?>
    <!-- Flatpickr (datas) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box card shadow">
            <div class="card-body">
                <div class="login-header">
                    <h1 class="mb-1"><?php echo htmlspecialchars($nome_sistema); ?></h1>
                    <p>Faça login para acessar o sistema</p>
                </div>

                <form class="login-form" action="autenticar.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token_login']); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="username" name="username" placeholder="Digite seu e-mail" maxlength="100" required autocomplete="username">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" maxlength="255" required autocomplete="current-password">
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Lembrar-me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">Entrar</button>

                    <div class="login-footer">
                        <a href="forgot-password.php" class="forgot-password">Esqueceu sua senha?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Flatpickr (datas - uso em formulários do sistema) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="assets/js/flatpickr-config.js"></script>
    <!-- Mensagens (SweetAlert2) e tratamento de erro no login -->
    <script src="assets/js/mensagens.js"></script>

    <script>
        window.LOGIN_FLASH = <?php echo json_encode($_SESSION['flash'] ?? null);
        unset($_SESSION['flash']); //IMPORTANTE: Limpa a mensagem da sessão após usar
        ?>;
    </script>


</body>
</html>
