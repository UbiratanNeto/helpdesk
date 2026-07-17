<?php
require_once 'verificar.php';
$pagina = $_GET['p'] ?? 'dashboard';

// Resgate de cores do banco de dados (carregadas via verificar.php -> conexao.php)
$primary = (!empty($cor_primaria)) ? $cor_primaria : '#4f46e5';
$secondary = (!empty($cor_secundaria)) ? $cor_secundaria : '#818cf8';
$bg_color = (!empty($cor_fundo)) ? $cor_fundo : '#f8fafc';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include 'includes/head.php'; ?>

    <!-- INJEÇÃO DINÂMICA DE CORES E RESET CRÍTICO DE ALINHAMENTO -->
    <style>
        :root {
            --cor-primaria: <?php echo $primary; ?>;
            --cor-secundaria: <?php echo $secondary; ?>;
            --bg-body: <?php echo $bg_color; ?>;

            /* Sincroniza com as variáveis do helpdesk-ui */
            --helpdesk-primary: <?php echo $primary; ?>;
            --helpdesk-secondary: <?php echo $secondary; ?>;
            --helpdesk-gradient: linear-gradient(135deg, <?php echo $primary; ?> 0%, <?php echo $secondary; ?> 100%);
        }

        /* Força o reset físico do Body contra conflitos de CSS externos/Bootstrap */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100vh !important;
            overflow: hidden !important;
            background-color: var(--bg-body) !important;
        }
    </style>
</head>

<body>

    <!-- 1. CONTAINER PAI (O que junta as duas colunas laterais) -->
    <div class="hd-layout">

        <!-- 2. COLUNA DA ESQUERDA: SIDEBAR -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- 3. COLUNA DA DIREITA: CONTEÚDO DINÂMICO, TOPBAR E FOOTER -->
        <div class="hd-layout__main">

            <!-- Barra Superior (Agora alinhada perfeitamente no topo direito) -->
            <?php include 'includes/topbar.php'; ?>

            <!-- Conteúdo da Página Atual (Dashboard, Chamados, etc) -->
            <main class="hd-container">
                <?php
                // Lista de páginas permitidas: qualquer valor de ?p= fora desta lista
                // é ignorado, mesmo que o texto pareça um caminho de arquivo válido.
                // Isso evita Local File Inclusion (ex.: ?p=../../conexao).
                $paginas_permitidas = ['dashboard', 'chamados', 'clientes', 'usuarios', 'relatorios', 'configuracoes'];

                if (!in_array($pagina, $paginas_permitidas, true)) {
                    $pagina = 'dashboard';
                }

                // Mesmo dentro da lista permitida, a página pode ainda não ter sido criada
                $arquivo_pagina = "paginas/{$pagina}.php";
                if (file_exists($arquivo_pagina)) {
                    include $arquivo_pagina;
                } else {
                    include 'paginas/404.php'; // Se não existir, exibe erro amigável
                }
                ?>
            </main>

            <!-- Rodapé Global -->
            <?php include 'includes/footer.php'; ?>

        </div> <!-- Fim de .hd-layout__main -->

    </div> <!-- Fim de .hd-layout -->

    <!-- Carregamento do script de controle do painel -->
    <script src="assets/js/painel.js"></script>
</body>

</html>