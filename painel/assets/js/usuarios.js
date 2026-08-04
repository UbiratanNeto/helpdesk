/**
 * painel/assets/js/usuarios.js
 * Gestão de Usuários: lista via DataTables (Bootstrap 5 + jQuery), cria/edita/exclui via fetch.
 *
 * Funções globais (novo/editar/excluir/salvar/visualizar), sem namespace: só carregamos este
 * arquivo na página de Usuários, então não há risco de colidir com o mesmo padrão em outras
 * páginas (chamados.js, clientes.js etc. terão suas próprias versões dessas funções).
 */

let tabela;
let idParaExcluir = null;

$(function () {
    const $tabela = $('#tabelaUsuarios');
    if ($tabela.length === 0) return; // Este script só faz sentido na página de Usuários

    tabela = $tabela.DataTable({
        ajax: {
            url: 'scripts/listar_usuarios.php',
            dataSrc: function (json) {
                if (!json.ok) {
                    Mensagens.erro('Erro', json.msg || 'Não foi possível carregar os usuários.');
                    return [];
                }
                return json.data;
            }
        },
        columns: [
            {
                data: 'foto_url', orderable: false, render: function (url) {
                    return '<img src="' + url + '" class="rounded-circle" style="width:2.25rem;height:2.25rem;object-fit:cover;" alt="">';
                }
            },
            { data: 'nome' },
            { data: 'email' },
            { data: 'telefone', render: function (v) { return v || '-'; } },
            { data: 'nivel' },
            {
                data: 'ativo', render: function (ativo) {
                    return ativo == 1
                        ? '<span class="badge bg-success">Ativo</span>'
                        : '<span class="badge bg-danger">Inativo</span>';
                }
            },
            {
                data: 'id', orderable: false, className: 'text-end', render: function (id) {
                    return '<div class="btn-group btn-group-sm">'
                        + '<button type="button" class="btn btn-outline-primary btn-visualizar" data-id="' + id + '" title="Visualizar"><i class="fa-solid fa-eye"></i></button>'
                        + '<button type="button" class="btn btn-outline-secondary btn-editar" data-id="' + id + '" title="Editar"><i class="fa-solid fa-pen"></i></button>'
                        + '<button type="button" class="btn btn-outline-danger btn-excluir" data-id="' + id + '" title="Excluir"><i class="fa-solid fa-trash"></i></button>'
                        + '</div>';
                }
            },
        ],
        order: [[1, 'asc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
    });

    // Delegação de eventos: os botões de ação são recriados a cada "draw" do DataTables
    $tabela.on('click', '.btn-visualizar', function () {
        visualizar($(this).data('id'));
    });
    $tabela.on('click', '.btn-editar', function () {
        editar($(this).data('id'));
    });
    $tabela.on('click', '.btn-excluir', function () {
        excluir($(this).data('id'));
    });

    $('#formUsuario').on('submit', function (e) {
        e.preventDefault();
        salvar(this);
    });

    $('#usuario_foto').on('change', function () {
        if (!this.files || !this.files[0]) return;
        const leitor = new FileReader();
        leitor.onload = function (e) {
            $('#previewFotoUsuario').attr('src', e.target.result);
        };
        leitor.readAsDataURL(this.files[0]);
    });

    $('#btnConfirmarExclusao').on('click', function () {
        confirmarExclusao();
    });
});

function novo() {
    const form = document.getElementById('formUsuario');
    form.reset();
    $('#usuario_id').val('');
    $('#modalUsuarioLabel').text('Cadastrar Usuário');
    $('#usuarioSenhaHint').text('(mínimo 6 caracteres)');
    $('#usuario_senha').prop('required', true);
    $('#previewFotoUsuario').attr('src', '../uploads/perfil/sem_foto.png');
    bootstrap.Modal.getOrCreateInstance('#modalUsuario').show();
}

function visualizar(id) {
    fetch('scripts/buscar_usuario.php?id=' + encodeURIComponent(id))
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.ok) {
                Mensagens.erro('Erro', dados.msg);
                return;
            }

            const u = dados.data;
            const ativo = u.ativo == 1;

            $('#verFotoUsuario').attr('src', u.foto_url);
            $('#verNome').text(u.nome || '-');
            $('#verStatus').text(ativo ? 'Ativo' : 'Inativo')
                .removeClass('bg-success bg-danger')
                .addClass(ativo ? 'bg-success' : 'bg-danger');
            $('#verEmail').text(u.email || '-');
            $('#verTelefone').text(u.telefone || '-');
            $('#verCpf').text(u.cpf || '-');
            $('#verNivel').text(u.nivel || '-');
            $('#verCep').text(u.cep || '-');
            $('#verEstado').text(u.estado || '-');
            $('#verCidade').text(u.cidade || '-');
            $('#verEndereco').text(u.endereco || '-');
            $('#verNumero').text(u.numero || '-');
            $('#verBairro').text(u.bairro || '-');
            $('#verComplemento').text(u.complemento || '-');

            bootstrap.Modal.getOrCreateInstance('#modalVisualizarUsuario').show();
        })
        .catch(function () {
            Mensagens.erro('Erro', 'Não foi possível carregar os dados do usuário.');
        });
}

function editar(id) {
    fetch('scripts/buscar_usuario.php?id=' + encodeURIComponent(id))
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.ok) {
                Mensagens.erro('Erro', dados.msg);
                return;
            }

            const u = dados.data;

            $('#usuario_id').val(u.id);
            $('#usuario_nome').val(u.nome || '');
            $('#usuario_email').val(u.email || '');
            $('#usuario_telefone').val(u.telefone || '');
            $('#usuario_cpf').val(u.cpf || '');
            $('#usuario_cep').val(u.cep || '');
            $('#usuario_estado').val(u.estado || '');
            $('#usuario_cidade').val(u.cidade || '');
            $('#usuario_bairro').val(u.bairro || '');
            $('#usuario_endereco').val(u.endereco || '');
            $('#usuario_numero').val(u.numero || '');
            $('#usuario_complemento').val(u.complemento || '');
            $('#usuario_nivel').val(u.nivel || 'Usuário');
            $('#usuario_ativo').val(String(u.ativo));

            $('#modalUsuarioLabel').text('Editar Usuário');
            $('#usuarioSenhaHint').text('(deixe em branco para não alterar)');
            $('#usuario_senha').prop('required', false).val('');

            $('#previewFotoUsuario').attr('src', u.foto_url);

            bootstrap.Modal.getOrCreateInstance('#modalUsuario').show();
        })
        .catch(function () {
            Mensagens.erro('Erro', 'Não foi possível carregar os dados do usuário.');
        });
}

function excluir(id) {
    idParaExcluir = id;
    bootstrap.Modal.getOrCreateInstance('#modalConfirmarExclusao').show();
}

function confirmarExclusao() {
    if (!idParaExcluir) return;

    const botao = document.getElementById('btnConfirmarExclusao');
    const textoOriginal = botao.textContent;
    botao.disabled = true;
    botao.textContent = 'Excluindo...';

    fetch('scripts/excluir_usuario.php', {
        method: 'POST',
        body: new URLSearchParams({ id: idParaExcluir })
    })
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            bootstrap.Modal.getOrCreateInstance('#modalConfirmarExclusao').hide();
            if (dados.ok) {
                tabela.ajax.reload(null, false);
                Mensagens.sucesso('Sucesso!', dados.msg);
            } else {
                Mensagens.erro('Erro', dados.msg);
            }
        })
        .catch(function () {
            bootstrap.Modal.getOrCreateInstance('#modalConfirmarExclusao').hide();
            Mensagens.erro('Erro de conexão', 'Não foi possível excluir agora. Tente novamente.');
        })
        .finally(function () {
            idParaExcluir = null;
            botao.disabled = false;
            botao.textContent = textoOriginal;
        });
}

function salvar(form) {
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
                bootstrap.Modal.getOrCreateInstance('#modalUsuario').hide();
                tabela.ajax.reload(null, false);
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
