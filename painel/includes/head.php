<?php
/**
 * painel/includes/head.php
 * Cabeçalho global do Painel
 */

// Se as variáveis não estiverem definidas no escopo atual, assume os fallbacks padrão
$primary   = $cor_primaria ?? '#4f46e5';
$secondary = $cor_secundaria ?? '#818cf8';

// Sanitização básica para garantir que as cores sejam hexadecimais válidos (#abc ou #abcdef)
if (!preg_match('/^#[a-fA-F0-9]{3,8}$/', $primary)) {
    $primary = '#4f46e5';
}
if (!preg_match('/^#[a-fA-F0-9]{3,8}$/', $secondary)) {
    $secondary = '#818cf8';
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel - <?php echo htmlspecialchars($nome_sistema ?? 'Helpdesk', ENT_QUOTES, 'UTF-8'); ?></title>

<!-- Importação do FontAwesome (Ícones) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- O seu arquivo ÚNICO de estilos, localizado na raiz de estilos do Helpdesk -->
<link rel="stylesheet" href="../css/main.css">

<!-- PONTE DE COMUNICAÇÃO: Injeta as variáveis de cor dinâmicas para o CSS -->
<style>
:root {
    --cor-primaria: <?php echo $primary; ?>;
    --cor-secundaria: <?php echo $secondary; ?>;
}
</style>

<!-- ChartJS para renderizar os gráficos nos dashboards -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>