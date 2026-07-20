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

    <!-- Fundo Escurecido da Modal (Invisível por padrão) -->
<div id="modalConfig" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
    
    <!-- Modal Editar Configurações -->
    <div class="hd-card" style="width: 650px; max-width: 95%; max-height: 90vh; overflow-y: auto;">
        
        <!-- Cabeçalho com Botão de Fechar -->
        <div class="hd-card__header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="hd-card__title">Editar Configurações do Sistema</h3>
            <button onclick="document.getElementById('modalConfig').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color: var(--muted-color);">&times;</button>
        </div>
        
        <div class="hd-card__body">
            <form action="configuracoes.php" method="POST">
                <div class="hd-form-grid">

                    <h4 class="hd-form-section-title">Dados do Sistema</h4>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="nome_sistema">Nome do Sistema</label>
                        <input type="text" id="nome_sistema" name="nome_sistema" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($nome_sistema ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome do Sistema">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="telefone_sistema">Telefone</label>
                        <input type="text" id="telefone_sistema" name="telefone_sistema" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($telefone_sistema ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Telefone">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="email_sistema">E-mail do Sistema</label>
                        <input type="email" id="email_sistema" name="email_sistema" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($email_sistema ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="E-mail do Sistema">
                    </div>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Cores do Painel</h4>

                    <div class="hd-field">
                        <label class="hd-field__label" for="cor_primaria">Cor Primária</label>
                        <div class="hd-color-swatch-group">
                            <input type="color" class="hd-color-swatch" id="cor_primaria_seletor" value="<?php echo htmlspecialchars($primary, ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true" tabindex="-1">
                            <input type="text" id="cor_primaria" name="cor_primaria" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($primary, ENT_QUOTES, 'UTF-8'); ?>" placeholder="#4f46e5">
                        </div>
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="cor_secundaria">Cor Secundária</label>
                        <div class="hd-color-swatch-group">
                            <input type="color" class="hd-color-swatch" id="cor_secundaria_seletor" value="<?php echo htmlspecialchars($secondary, ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true" tabindex="-1">
                            <input type="text" id="cor_secundaria" name="cor_secundaria" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($secondary, ENT_QUOTES, 'UTF-8'); ?>" placeholder="#818cf8">
                        </div>
                    </div>
                    <p class="hd-field__hint" style="grid-column: 1 / -1; margin-top: -0.5rem;">Dica: você pode colar o HEX no campo de texto.</p>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Configuração SMTP</h4>

                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_host">Servidor SMTP (Host)</label>
                        <input type="text" id="smtp_host" name="smtp_host" class="hd-field__input hd-field__input--plain" placeholder="Servidor SMTP (Host)">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_porta">Porta SMTP</label>
                        <input type="number" id="smtp_porta" name="smtp_porta" class="hd-field__input hd-field__input--plain" placeholder="Porta SMTP">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_senha">Senha SMTP</label>
                        <input type="password" id="smtp_senha" name="smtp_senha" class="hd-field__input hd-field__input--plain" placeholder="Senha SMTP">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_seguranca">Segurança</label>
                        <select id="smtp_seguranca" name="smtp_seguranca" class="hd-field__input hd-field__input--plain">
                            <option value="">Nenhuma</option>
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                        </select>
                    </div>
                    <p class="hd-field__hint" style="grid-column: 1 / -1; margin-top: -0.5rem;">Deixe em branco para não alterar (quando você implementar o update).</p>
                </div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="hd-btn hd-btn--ghost" style="flex: 1;" onclick="document.getElementById('modalConfig').style.display='none'">Cancelar</button>
                    <button type="submit" class="hd-btn hd-btn--primary" style="flex: 1;">Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fundo Escurecido da Modal de Perfil (Invisível por padrão) -->
<div id="modalPerfil" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
    
    <!-- Modal Editar Perfil -->
    <div class="hd-card" style="width: 700px; max-width: 95%; max-height: 90vh; overflow-y: auto;">
        
        <!-- Cabeçalho com Botão de Fechar -->
        <div class="hd-card__header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="hd-card__title">Editar Perfil do Usuário</h3>
            <button onclick="document.getElementById('modalPerfil').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color: var(--muted-color);">&times;</button>
        </div>
        
        <div class="hd-card__body">
            <form action="perfil.php" method="POST">
                <div class="hd-form-grid">

                    <h4 class="hd-form-section-title">Dados Pessoais</h4>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="perfil_nome">Nome Completo</label>
                        <input type="text" id="perfil_nome" name="nome" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome Completo">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_email">E-mail</label>
                        <input type="email" id="perfil_email" name="email" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="E-mail">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_cpf">CPF</label>
                        <input type="text" id="perfil_cpf" name="cpf" class="hd-field__input hd-field__input--plain" placeholder="CPF">
                    </div>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="perfil_telefone">Telefone</label>
                        <input type="text" id="perfil_telefone" name="telefone" class="hd-field__input hd-field__input--plain" placeholder="Telefone">
                    </div>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Endereço</h4>

                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_cep">CEP</label>
                        <input type="text" id="perfil_cep" name="cep" class="hd-field__input hd-field__input--plain" placeholder="CEP">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_estado">Estado</label>
                        <input type="text" id="perfil_estado" name="estado" class="hd-field__input hd-field__input--plain" placeholder="UF" maxlength="2">
                    </div>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="perfil_cidade">Cidade</label>
                        <input type="text" id="perfil_cidade" name="cidade" class="hd-field__input hd-field__input--plain" placeholder="Cidade">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_bairro">Bairro</label>
                        <input type="text" id="perfil_bairro" name="bairro" class="hd-field__input hd-field__input--plain" placeholder="Bairro">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_endereco">Endereço</label>
                        <input type="text" id="perfil_endereco" name="endereco" class="hd-field__input hd-field__input--plain" placeholder="Endereço">
                    </div>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="perfil_numero">Número</label>
                        <input type="text" id="perfil_numero" name="numero" class="hd-field__input hd-field__input--plain" placeholder="Número">
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="hd-btn hd-btn--ghost" style="flex: 1;" onclick="document.getElementById('modalPerfil').style.display='none'">Cancelar</button>
                    <button type="submit" class="hd-btn hd-btn--primary" style="flex: 1;">Atualizar Perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sincroniza o seletor de cor visual com o campo de texto hex, nos dois sentidos -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pares = [
        ['cor_primaria_seletor', 'cor_primaria'],
        ['cor_secundaria_seletor', 'cor_secundaria'],
    ];
    pares.forEach(function ([idSeletor, idTexto]) {
        const seletor = document.getElementById(idSeletor);
        const texto = document.getElementById(idTexto);
        if (!seletor || !texto) return;

        seletor.addEventListener('input', function () {
            texto.value = seletor.value;
        });
        texto.addEventListener('input', function () {
            if (/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/.test(texto.value)) {
                seletor.value = texto.value;
            }
        });
    });
});
</script>

</body>

</html>