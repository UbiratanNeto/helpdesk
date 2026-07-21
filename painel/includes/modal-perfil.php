<?php
/**
 * painel/includes/modal-perfil.php
 * Modal "Editar Perfil do Usuário" — acionado pelo link no dropdown do topbar.
 * Depende de $_SESSION['nome']/['email']/['foto'], já preenchidos pelo verificar.php.
 */
$perfil_foto_arquivo = basename($_SESSION['foto'] ?? '');
$perfil_foto_existe  = $perfil_foto_arquivo !== '' && file_exists(__DIR__ . '/../../uploads/perfil/' . $perfil_foto_arquivo);
?>
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
            <form id="formPerfil" action="scripts/salvar_perfil.php" method="POST" enctype="multipart/form-data">
                <div class="hd-form-grid">

                    <h4 class="hd-form-section-title">Dados Pessoais</h4>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_nome">Nome Completo</label>
                        <input type="text" id="perfil_nome" name="nome" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome Completo">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_telefone">Telefone</label>
                        <input type="text" id="perfil_telefone" name="telefone" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['telefone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Telefone">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_email">E-mail</label>
                        <input type="email" id="perfil_email" name="email" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="E-mail">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_cpf">CPF</label>
                        <input type="text" id="perfil_cpf" name="cpf" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="CPF">
                    </div>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Endereço</h4>

                    <?php $perfil_estado_atual = $_SESSION['estado'] ?? ''; ?>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_cep">CEP</label>
                        <input type="text" id="perfil_cep" name="cep" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['cep'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="CEP">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_estado">Estado</label>
                        <select id="perfil_estado" name="estado" class="hd-field__input hd-field__input--plain">
                            <option value="" <?php echo $perfil_estado_atual === '' ? 'selected' : ''; ?>>Selecione o Estado</option>
                            <option value="AC" <?php echo $perfil_estado_atual === 'AC' ? 'selected' : ''; ?>>Acre</option>
                            <option value="AL" <?php echo $perfil_estado_atual === 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                            <option value="AP" <?php echo $perfil_estado_atual === 'AP' ? 'selected' : ''; ?>>Amapá</option>
                            <option value="AM" <?php echo $perfil_estado_atual === 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                            <option value="BA" <?php echo $perfil_estado_atual === 'BA' ? 'selected' : ''; ?>>Bahia</option>
                            <option value="CE" <?php echo $perfil_estado_atual === 'CE' ? 'selected' : ''; ?>>Ceará</option>
                            <option value="DF" <?php echo $perfil_estado_atual === 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                            <option value="ES" <?php echo $perfil_estado_atual === 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                            <option value="GO" <?php echo $perfil_estado_atual === 'GO' ? 'selected' : ''; ?>>Goiás</option>
                            <option value="MA" <?php echo $perfil_estado_atual === 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                            <option value="MT" <?php echo $perfil_estado_atual === 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                            <option value="MS" <?php echo $perfil_estado_atual === 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                            <option value="MG" <?php echo $perfil_estado_atual === 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                            <option value="PA" <?php echo $perfil_estado_atual === 'PA' ? 'selected' : ''; ?>>Pará</option>
                            <option value="PB" <?php echo $perfil_estado_atual === 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                            <option value="PR" <?php echo $perfil_estado_atual === 'PR' ? 'selected' : ''; ?>>Paraná</option>
                            <option value="PE" <?php echo $perfil_estado_atual === 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                            <option value="PI" <?php echo $perfil_estado_atual === 'PI' ? 'selected' : ''; ?>>Piauí</option>
                            <option value="RJ" <?php echo $perfil_estado_atual === 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                            <option value="RN" <?php echo $perfil_estado_atual === 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                            <option value="RS" <?php echo $perfil_estado_atual === 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                            <option value="RO" <?php echo $perfil_estado_atual === 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                            <option value="RR" <?php echo $perfil_estado_atual === 'RR' ? 'selected' : ''; ?>>Roraima</option>
                            <option value="SC" <?php echo $perfil_estado_atual === 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                            <option value="SP" <?php echo $perfil_estado_atual === 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                            <option value="SE" <?php echo $perfil_estado_atual === 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                            <option value="TO" <?php echo $perfil_estado_atual === 'TO' ? 'selected' : ''; ?>>Tocantins</option>
                        </select>
                    </div>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="perfil_cidade">Cidade</label>
                        <input type="text" id="perfil_cidade" name="cidade" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['cidade'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cidade">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_bairro">Bairro</label>
                        <input type="text" id="perfil_bairro" name="bairro" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['bairro'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Bairro">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_endereco">Endereço</label>
                        <input type="text" id="perfil_endereco" name="endereco" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['endereco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Endereço">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_numero">Número</label>
                        <input type="text" id="perfil_numero" name="numero" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['numero'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Número">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="perfil_complemento">Complemento</label>
                        <input type="text" id="perfil_complemento" name="complemento" class="hd-field__input hd-field__input--plain" value="<?php echo htmlspecialchars($_SESSION['complemento'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Complemento">
                    </div>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Foto do Perfil</h4>

                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <?php if ($perfil_foto_existe): ?>
                                <img src="../uploads/perfil/<?php echo htmlspecialchars($perfil_foto_arquivo, ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width: 3.5rem; height: 3.5rem; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                            <?php else: ?>
                                <div class="user-avatar-placeholder" style="width: 3.5rem; height: 3.5rem; font-size: 1.3rem;">
                                    <?php echo htmlspecialchars(strtoupper(substr(trim($_SESSION['nome'] ?? 'U'), 0, 1)), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="perfil_foto" name="foto" accept=".png,.jpg,.jpeg,.webp" class="hd-field__input hd-field__input--plain" style="flex: 1;">
                        </div>
                        <p class="hd-field__hint">PNG, JPG ou WEBP — até 2MB. Deixe em branco para manter a foto atual.</p>
                    </div>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Trocar Senha</h4>

                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="perfil_nova_senha">Nova Senha</label>
                        <input type="password" id="perfil_nova_senha" name="nova_senha" class="hd-field__input hd-field__input--plain" placeholder="Nova Senha" autocomplete="new-password" minlength="3">
                        <p class="hd-field__hint">Deixe em branco para não alterar.</p>
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

<!-- Biblioteca IMask, hospedada localmente (deixou de depender do CDN externo) -->
<script src="assets/js/imask.min.js"></script>

<!-- Seus scripts de Máscara e Funções/ViaCEP -->
<script src="assets/js/masks.js"></script>
<script src="assets/js/functions.js"></script>

<!-- O envio do formulário (fetch + upload de arquivo + alerta de sucesso/erro) é tratado
     de forma genérica por painel/assets/js/ajax-form.js, que já reconhece este form pela action. -->
