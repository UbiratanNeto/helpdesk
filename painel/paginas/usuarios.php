<?php
/**
 * painel/paginas/usuarios.php
 * Gestão de Usuários — lista, cria, edita e exclui usuários com acesso ao sistema.
 */
?>
<div class="hd-welcome">
    <h1 class="hd-welcome__title">Gestão de Usuários</h1>
    <p class="hd-welcome__subtitle">Gerencie todos os usuários com acesso ao sistema</p>
</div>

<div class="hd-card">
    <div class="hd-card__header hd-card__header--row">
        <h2 class="hd-card__title" style="font-size: 1.15rem;">
            <i class="fa-solid fa-users" style="color: var(--cor-primaria); margin-right: 0.5rem;"></i>
            Usuários
        </h2>
        <button type="button" class="hd-btn hd-btn--primary hd-btn--sm" onclick="novoUsuario()">
            <i class="fa-solid fa-plus" style="margin-right: 0.4rem;"></i>Novo Usuário
        </button>
    </div>
    <div class="hd-card__body">
        <div class="hd-table-responsive">
            <table class="hd-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Nível</th>
                        <th>Status</th>
                        <th style="text-align: right;">Ações</th>
                    </tr>
                </thead>
                <tbody id="corpoTabelaUsuarios">
                    <tr><td colspan="7" style="text-align: center; color: var(--muted-color); padding: 2rem;">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Usuário (Cadastrar / Editar) — mesmo padrão de display:none dos outros modais do painel -->
<div id="modalUsuario" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
    <div class="hd-card" style="width: 700px; max-width: 95%; max-height: 90vh; overflow-y: auto;">

        <div class="hd-card__header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="hd-card__title" id="modalUsuarioTitulo">Cadastrar Usuário</h3>
            <button type="button" onclick="fecharModalUsuario()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color: var(--muted-color);">&times;</button>
        </div>

        <div class="hd-card__body">
            <form id="formUsuario" enctype="multipart/form-data">
                <input type="hidden" id="usuario_id" name="id">

                <div class="hd-form-grid">
                    <h4 class="hd-form-section-title">Dados Pessoais</h4>

                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img id="previewFotoUsuario" src="../uploads/perfil/sem_foto.png" alt="" style="width: 3.5rem; height: 3.5rem; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-line);">
                            <input type="file" id="usuario_foto" name="foto" accept=".png,.jpg,.jpeg,.webp" class="hd-field__input hd-field__input--plain" style="flex: 1;">
                        </div>
                        <p class="hd-field__hint">PNG, JPG ou WEBP — até 2MB.</p>
                    </div>

                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="usuario_nome">Nome Completo *</label>
                        <input type="text" id="usuario_nome" name="nome" class="hd-field__input hd-field__input--plain" required>
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_email">E-mail *</label>
                        <input type="email" id="usuario_email" name="email" class="hd-field__input hd-field__input--plain" required>
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_cpf">CPF</label>
                        <input type="text" id="usuario_cpf" name="cpf" class="hd-field__input hd-field__input--plain" placeholder="000.000.000-00">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_telefone">Telefone</label>
                        <input type="text" id="usuario_telefone" name="telefone" class="hd-field__input hd-field__input--plain" placeholder="(00) 00000-0000">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_senha">Senha <span id="usuarioSenhaHint" style="font-weight: 400; color: var(--muted-color); font-size: 0.78rem;">(mínimo 6 caracteres)</span></label>
                        <input type="password" id="usuario_senha" name="senha" class="hd-field__input hd-field__input--plain" autocomplete="new-password" minlength="6">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_nivel">Nível de Acesso *</label>
                        <select id="usuario_nivel" name="nivel" class="hd-field__input hd-field__input--plain" required>
                            <option value="Usuário">Usuário</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_ativo">Status *</label>
                        <select id="usuario_ativo" name="ativo" class="hd-field__input hd-field__input--plain" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>

                    <div class="hd-form-divider"></div>
                    <h4 class="hd-form-section-title">Endereço</h4>

                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_cep">CEP</label>
                        <input type="text" id="usuario_cep" name="cep" class="hd-field__input hd-field__input--plain" placeholder="CEP">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_estado">Estado</label>
                        <select id="usuario_estado" name="estado" class="hd-field__input hd-field__input--plain">
                            <option value="">Selecione o Estado</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                    <div class="hd-field" style="grid-column: 1 / -1;">
                        <label class="hd-field__label" for="usuario_cidade">Cidade</label>
                        <input type="text" id="usuario_cidade" name="cidade" class="hd-field__input hd-field__input--plain" placeholder="Cidade">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_bairro">Bairro</label>
                        <input type="text" id="usuario_bairro" name="bairro" class="hd-field__input hd-field__input--plain" placeholder="Bairro">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_endereco">Endereço</label>
                        <input type="text" id="usuario_endereco" name="endereco" class="hd-field__input hd-field__input--plain" placeholder="Endereço">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_numero">Número</label>
                        <input type="text" id="usuario_numero" name="numero" class="hd-field__input hd-field__input--plain" placeholder="Número">
                    </div>
                    <div class="hd-field">
                        <label class="hd-field__label" for="usuario_complemento">Complemento</label>
                        <input type="text" id="usuario_complemento" name="complemento" class="hd-field__input hd-field__input--plain" placeholder="Complemento">
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="hd-btn hd-btn--ghost" style="flex: 1;" onclick="fecharModalUsuario()">Cancelar</button>
                    <button type="submit" class="hd-btn hd-btn--primary" style="flex: 1;" id="btnSalvarUsuario">Salvar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/usuarios.js"></script>
