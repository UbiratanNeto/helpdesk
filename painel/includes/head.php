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

<!-- Bootstrap 5 + DataTables: carregados só nas páginas que realmente usam (Usuários, Cargos),
     igual ao Chart.js abaixo — o resto do painel continua hd-*/vanilla JS. -->
<?php if (in_array($pagina ?? '', ['usuarios', 'cargos'], true)): ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<?php endif; ?>

<!-- O seu arquivo ÚNICO de estilos, localizado na raiz de estilos do Helpdesk -->
<link rel="stylesheet" href="../css/main.css">

<!-- ChartJS: carregado só na página que realmente usa gráfico -->
<?php if (($pagina ?? '') === 'dashboard'): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php endif; ?>

<!-- SweetAlert2 + Mensagens: usados pelo ajax-form.js para dar retorno visual
     ao salvar os modais (Configurações, Perfil), sem sair da página. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/mensagens.js"></script>