/**
 * painel/assets/js/usuarios.js
 * Gestão de Usuários: lista, cria, edita e exclui — tudo via fetch, sem jQuery/DataTables
 * (o projeto não carrega nenhuma das duas em nenhuma outra página do painel).
 */

let usuarioEmEdicao = false;

document.addEventListener('DOMContentLoaded', function () {
    const tabela = document.getElementById('corpoTabelaUsuarios');
    if (!tabela) return; // Este script só faz sentido na página de Usuários

    carregarUsuarios();

    const form = document.getElementById('formUsuario');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        salvarUsuario(form);
    });

    const inputFoto = document.getElementById('usuario_foto');
    inputFoto.addEventListener('change', function () {
        if (!this.files || !this.files[0]) return;
        const leitor = new FileReader();
        leitor.onload = function (e) {
            document.getElementById('previewFotoUsuario').src = e.target.result;
        };
        leitor.readAsDataURL(this.files[0]);
    });
});

function carregarUsuarios() {
    const tabela = document.getElementById('corpoTabelaUsuarios');
    tabela.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--muted-color); padding: 2rem;">Carregando...</td></tr>';

    fetch('scripts/listar_usuarios.php')
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.ok || dados.data.length === 0) {
                tabela.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--muted-color); padding: 2rem;">Nenhum usuário encontrado.</td></tr>';
                return;
            }
            tabela.innerHTML = '';
            dados.data.forEach(function (usuario) {
                tabela.appendChild(criarLinhaUsuario(usuario));
            });
        })
        .catch(function () {
            tabela.innerHTML = '<tr><td colspan="7" style="text-align: center; color: #ef4444; padding: 2rem;">Erro ao carregar usuários.</td></tr>';
        });
}

function criarLinhaUsuario(usuario) {
    const tr = document.createElement('tr');

    // Foto
    const tdFoto = document.createElement('td');
    const img = document.createElement('img');
    img.src = usuario.foto_url;
    img.alt = '';
    img.style.cssText = 'width: 2.25rem; height: 2.25rem; border-radius: 50%; object-fit: cover;';
    tdFoto.appendChild(img);

    // Nome, e-mail, telefone, nível (texto simples, seguro contra HTML no dado)
    const tdNome = document.createElement('td');
    tdNome.textContent = usuario.nome;

    const tdEmail = document.createElement('td');
    tdEmail.textContent = usuario.email;

    const tdTelefone = document.createElement('td');
    tdTelefone.textContent = usuario.telefone || '-';

    const tdNivel = document.createElement('td');
    tdNivel.textContent = usuario.nivel;

    // Status (badge reaproveitado do sistema — mesmas classes usadas nos chamados do dashboard)
    const tdStatus = document.createElement('td');
    const badge = document.createElement('span');
    badge.className = 'hd-badge ' + (usuario.ativo == 1 ? 'hd-badge--success' : 'hd-badge--danger');
    badge.textContent = usuario.ativo == 1 ? 'Ativo' : 'Inativo';
    tdStatus.appendChild(badge);

    // Ações
    const tdAcoes = document.createElement('td');
    tdAcoes.style.textAlign = 'right';

    const btnEditar = document.createElement('button');
    btnEditar.type = 'button';
    btnEditar.className = 'hd-table__action-btn';
    btnEditar.title = 'Editar';
    btnEditar.innerHTML = '<i class="fa-solid fa-pen"></i>';
    btnEditar.addEventListener('click', function () { editarUsuario(usuario.id); });

    const btnExcluir = document.createElement('button');
    btnExcluir.type = 'button';
    btnExcluir.className = 'hd-table__action-btn hd-table__action-btn--danger';
    btnExcluir.title = 'Excluir';
    btnExcluir.innerHTML = '<i class="fa-solid fa-trash"></i>';
    btnExcluir.addEventListener('click', function () { excluirUsuario(usuario.id); });

    tdAcoes.appendChild(btnEditar);
    tdAcoes.appendChild(btnExcluir);

    tr.append(tdFoto, tdNome, tdEmail, tdTelefone, tdNivel, tdStatus, tdAcoes);
    return tr;
}

function abrirModalUsuario() {
    document.getElementById('modalUsuario').style.display = 'flex';
}

function fecharModalUsuario() {
    document.getElementById('modalUsuario').style.display = 'none';
}

function novoUsuario() {
    usuarioEmEdicao = false;
    const form = document.getElementById('formUsuario');
    form.reset();
    document.getElementById('usuario_id').value = '';
    document.getElementById('modalUsuarioTitulo').textContent = 'Cadastrar Usuário';
    document.getElementById('usuarioSenhaHint').textContent = '(mínimo 6 caracteres)';
    document.getElementById('usuario_senha').setAttribute('required', 'required');
    document.getElementById('previewFotoUsuario').src = '../uploads/perfil/sem_foto.png';
    abrirModalUsuario();
}

function editarUsuario(id) {
    fetch('scripts/buscar_usuario.php?id=' + encodeURIComponent(id))
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.ok) {
                Mensagens.erro('Erro', dados.msg);
                return;
            }

            usuarioEmEdicao = true;
            const u = dados.data;

            document.getElementById('usuario_id').value = u.id;
            document.getElementById('usuario_nome').value = u.nome || '';
            document.getElementById('usuario_email').value = u.email || '';
            document.getElementById('usuario_telefone').value = u.telefone || '';
            document.getElementById('usuario_cpf').value = u.cpf || '';
            document.getElementById('usuario_cep').value = u.cep || '';
            document.getElementById('usuario_estado').value = u.estado || '';
            document.getElementById('usuario_cidade').value = u.cidade || '';
            document.getElementById('usuario_bairro').value = u.bairro || '';
            document.getElementById('usuario_endereco').value = u.endereco || '';
            document.getElementById('usuario_numero').value = u.numero || '';
            document.getElementById('usuario_complemento').value = u.complemento || '';
            document.getElementById('usuario_nivel').value = u.nivel || 'Usuário';
            document.getElementById('usuario_ativo').value = String(u.ativo);

            document.getElementById('modalUsuarioTitulo').textContent = 'Editar Usuário';
            document.getElementById('usuarioSenhaHint').textContent = '(deixe em branco para não alterar)';
            const inputSenha = document.getElementById('usuario_senha');
            inputSenha.removeAttribute('required');
            inputSenha.value = '';

            document.getElementById('previewFotoUsuario').src = u.foto_url;

            abrirModalUsuario();
        })
        .catch(function () {
            Mensagens.erro('Erro', 'Não foi possível carregar os dados do usuário.');
        });
}

function excluirUsuario(id) {
    if (!confirm('Tem certeza que deseja excluir este usuário?')) return;

    fetch('scripts/excluir_usuario.php', {
        method: 'POST',
        body: new URLSearchParams({ id: id })
    })
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (dados.ok) {
                carregarUsuarios();
            } else {
                Mensagens.erro('Erro', dados.msg);
            }
        })
        .catch(function () {
            Mensagens.erro('Erro de conexão', 'Não foi possível excluir agora. Tente novamente.');
        });
}

function salvarUsuario(form) {
    const botao = document.getElementById('btnSalvarUsuario');
    const textoOriginal = botao.textContent;
    botao.disabled = true;
    botao.textContent = 'Salvando...';

    fetch('scripts/salvar_usuario.php', {
        method: 'POST',
        body: new FormData(form)
    })
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (dados.ok) {
                fecharModalUsuario();
                carregarUsuarios();
                Mensagens.sucesso('Sucesso!', dados.msg);
            } else {
                Mensagens.erro('Atenção', dados.msg);
            }
        })
        .catch(function () {
            Mensagens.erro('Erro de conexão', 'Não foi possível salvar agora. Tente novamente.');
        })
        .finally(function () {
            botao.disabled = false;
            botao.textContent = textoOriginal;
        });
}
