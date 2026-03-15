<?php
/**
 * Painel - área restrita (requer login)
 * Verificação de sessão e exibição dos dados do usuário
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- Verificação de sessão: exige usuário logado ----------
if (empty($_SESSION['id'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../conexao.php';

// ---------- Recuperar dados atuais do usuário no banco ----------
$stmt = $pdo->prepare("SELECT id, nome, email, telefone, cpf, nivel, ativo, empresa, data_cadastro FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $_SESSION['id']]);
$usuario = $stmt->fetch();

if (!$usuario) {
    // Usuário foi removido do banco; encerra sessão e redireciona para login
    $_SESSION = [];
    session_destroy();
    header('Location: ../index.php?erro=sessao');
    exit;
}

if (empty($usuario['ativo']) || $usuario['ativo'] === '0') {
    // Conta inativada; encerra sessão
    $_SESSION = [];
    session_destroy();
    header('Location: ../index.php?erro=inativo');
    exit;
}

// Atualiza dados na sessão com o que está no banco (nome/email podem ter mudado)
$_SESSION['nome']       = $usuario['nome'];
$_SESSION['email']     = $usuario['email'];
$_SESSION['nivel']     = $usuario['nivel'];
$_SESSION['id_empresa'] = (int) $usuario['empresa'];

$nome_sessao   = htmlspecialchars($_SESSION['nome'] ?? '', ENT_QUOTES, 'UTF-8');
$email_sessao  = htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8');
$nivel_sessao  = htmlspecialchars($_SESSION['nivel'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - <?php echo htmlspecialchars($nome_sistema, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h4 mb-0">Painel</h1>
            <a href="../logout.php" class="btn btn-outline-danger">Sair</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <strong>Dados do usuário</strong>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Nome:</strong> <?php echo $nome_sessao; ?></p>
                <p class="mb-1"><strong>E-mail:</strong> <?php echo $email_sessao; ?></p>
                <p class="mb-1"><strong>Nível:</strong> <?php echo $nivel_sessao; ?></p>
                <p class="mb-1"><strong>Telefone:</strong> <?php echo htmlspecialchars($usuario['telefone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-1"><strong>CPF:</strong> <?php echo htmlspecialchars($usuario['cpf'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-0"><strong>Cadastro em:</strong> <?php echo $usuario['data_cadastro'] ? date('d/m/Y H:i', strtotime($usuario['data_cadastro'])) : '—'; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
