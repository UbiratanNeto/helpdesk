<?php
/**
 * painel/includes/modal-configuracoes.php
 * Modal "Editar Configurações do Sistema" — acionado pelo link no dropdown do topbar.
 * Depende de variáveis já carregadas em conexao.php/verificar.php:
 * $nome_sistema, $telefone_sistema, $email_sistema, $primary, $secondary,
 * $smtp_host, $smtp_porta, $smtp_seguranca
 */
?>
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
            <form id="formConfiguracoes" action="scripts/salvar_config.php" method="POST">
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
                        <input type="text" id="smtp_host" name="smtp_host" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($smtp_host ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Servidor SMTP (Host)">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_porta">Porta SMTP</label>
                        <input type="number" id="smtp_porta" name="smtp_porta" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars((string) ($smtp_porta ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Porta SMTP">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_senha">Senha SMTP</label>
                        <!-- Propositalmente sem "value": nunca imprimimos a senha salva de volta no formulário -->
                        <input type="password" id="smtp_senha" name="smtp_senha" class="hd-field__input hd-field__input--plain" placeholder="Senha SMTP" autocomplete="new-password">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="smtp_seguranca">Segurança</label>
                        <?php $smtp_seguranca_atual = $smtp_seguranca ?? ''; ?>
                        <select id="smtp_seguranca" name="smtp_seguranca" class="hd-field__input hd-field__input--plain">
                            <option value="" <?php echo $smtp_seguranca_atual === '' ? 'selected' : ''; ?>>Nenhuma</option>
                            <option value="tls" <?php echo $smtp_seguranca_atual === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo $smtp_seguranca_atual === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        </select>
                    </div>
                    <p class="hd-field__hint" style="grid-column: 1 / -1; margin-top: -0.5rem;">Deixe a senha em branco para não alterar (quando você implementar o update).</p>
                </div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="hd-btn hd-btn--ghost" style="flex: 1;" onclick="document.getElementById('modalConfig').style.display='none'">Cancelar</button>
                    <button type="submit" class="hd-btn hd-btn--primary" style="flex: 1;">Salvar Configurações</button>
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

<!-- SweetAlert2: usado pra dar retorno visual sem sair da página -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Envia o formulário via fetch (o back-end responde em JSON) em vez de deixar o navegador
     navegar pra scripts/salvar_config.php, que é o que causava a tela crua de JSON. -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formConfiguracoes');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const botao = form.querySelector('button[type="submit"]');
        const textoOriginal = botao.textContent;
        botao.disabled = true;
        botao.textContent = 'Salvando...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
            .then(function (resposta) { return resposta.json(); })
            .then(function (dados) {
                if (dados.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: dados.msg,
                        confirmButtonColor: '#4f46e5'
                    }).then(function () {
                        // Recarrega pra refletir imediatamente nome/cores em toda a tela (sidebar, título, etc.)
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: dados.msg, confirmButtonColor: '#4f46e5' });
                }
            })
            .catch(function () {
                Swal.fire({ icon: 'error', title: 'Erro de conexão', text: 'Não foi possível salvar agora. Tente novamente.', confirmButtonColor: '#4f46e5' });
            })
            .finally(function () {
                botao.disabled = false;
                botao.textContent = textoOriginal;
            });
    });
});
</script>
