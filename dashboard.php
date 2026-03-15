<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - <?php echo htmlspecialchars($nome_sistema); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <p class="mb-1">Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong> (<?php echo htmlspecialchars($_SESSION['usuario_nivel']); ?>).</p>
        <a href="logout.php" class="btn btn-outline-secondary">Sair</a>
    </div>
</body>
</html>
