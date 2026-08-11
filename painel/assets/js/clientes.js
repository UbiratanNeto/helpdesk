/**
 * painel/assets/js/clientes.js
 * Gestão de Clientes: lista via DataTables (Bootstrap 5 + jQuery), cria/edita/exclui via fetch.
 *
 * Mesmas funções globais genéricas de usuarios.js/cargos.js (novo/editar/excluir/salvar/visualizar) —
 * só carregamos este arquivo na página de Clientes, então não há colisão com as outras páginas.
 * Diferença importante: "ativo" aqui é texto ("Sim"/"Não"), não 1/0 como em usuarios/cargos —
 * é assim que a tabela clientes foi definida.
 */

let tabela;
let idParaExcluir = null;

$(function () {
    const $tabela = $('#tabelaClientes');
    if ($tabela.length === 0) return; // Este script só faz sentido na página de Clientes

    tabela = $tabela.DataTable({
        ajax: {
            url: 'scripts/clientes/listar.php',
            dataSrc: function (json) {
                if (!json.ok) {
                    Mensagens.erro('Erro', json.msg || 'Não foi possível carregar os clientes.');
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
            { data: 'email', render: function (v) { return v || '-'; } },
            { data: 'telefone', render: function (v) { return v || '-'; } },
            { data: 'tipo', render: function (v) { return v || '-'; } },
            {
                data: 'ativo', render: function (ativo) {
                    return ativo === 'Sim'
                        ? '<span class="badge bg-success">Sim</span>'
                        : '<span class="badge bg-danger">Não</span>';
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

    $('#formCliente').on('submit', function (e) {
        e.preventDefault();
        salvar(this);
    });

    $('#cliente_foto').on('change', function () {
        if (!this.files || !this.files[0]) return;
        const leitor = new FileReader();
        leitor.onload = function (e) {
            $('#previewFotoCliente').attr('src', e.target.result);
        };
        leitor.readAsDataURL(this.files[0]);
    });

    $('#btnConfirmarExclusao').on('click', function () {
        confirmarExclusao();
    });
});

function novo() {
    const form = document.getElementById('formCliente');
    form.reset();
    $('#cliente_id').val('');
    $('#modalClienteLabel').text('Cadastrar Cliente');
    $('#previewFotoCliente').attr('src', '../uploads/clientes/sem_foto.png');
    bootstrap.Modal.getOrCreateInstance('#modalCliente').show();
}

function visualizar(id) {
    fetch('scripts/clientes/buscar.php?id=' + encodeURIComponent(id))
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.ok) {
                Mensagens.erro('Erro', dados.msg);
                return;
            }

            const c = dados.data;
            const ativo = c.ativo === 'Sim';

            $('#verFotoCliente').attr('src', c.foto_url);
            $('#verNome').text(c.nome || '-');
            $('#verStatus').text(ativo ? 'Ativo' : 'Inativo')
                .removeClass('bg-success bg-danger')
                .addClass(ativo ? 'bg-success' : 'bg-danger');
            $('#verEmail').text(c.email || '-');
            $('#verTelefone').text(c.telefone || '-');
            $('#verCpfCnpj').text(c.cpf_cnpj || '-');
            $('#verTipo').text(c.tipo || '-');
            $('#verCep').text(c.cep || '-');
            $('#verEstado').text(c.estado || '-');
            $('#verCidade').text(c.cidade || '-');
            $('#verEndereco').text(c.endereco || '-');
            $('#verNumero').text(c.numero || '-');
            $('#verBairro').text(c.bairro || '-');
            $('#verComplemento').text(c.complemento || '-');
            $('#verObservacoes').text(c.observacoes || '-');

            bootstrap.Modal.getOrCreateInstance('#modalVisualizarCliente').show();
        })
        .catch(function () {
            Mensagens.erro('Erro', 'Não foi possível carregar os dados do cliente.');
        });
}

function editar(id) {
    fetch('scripts/clientes/buscar.php?id=' + encodeURIComponent(id))
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (!dados.ok) {
                Mensagens.erro('Erro', dados.msg);
                return;
            }

            const c = dados.data;

            $('#cliente_id').val(c.id);
            $('#cliente_nome').val(c.nome || '');
            $('#cliente_telefone').val(c.telefone || '');
            $('#cliente_cpf_cnpj').val(c.cpf_cnpj || '');
            $('#cliente_email').val(c.email || '');
            $('#cliente_tipo').val(c.tipo || 'Pessoa Física');
            $('#cliente_ativo').val(c.ativo || 'Sim');
            $('#cliente_cep').val(c.cep || '');
            $('#cliente_estado').val(c.estado || '');
            $('#cliente_cidade').val(c.cidade || '');
            $('#cliente_endereco').val(c.endereco || '');
            $('#cliente_numero').val(c.numero || '');
            $('#cliente_bairro').val(c.bairro || '');
            $('#cliente_complemento').val(c.complemento || '');
            $('#cliente_observacoes').val(c.observacoes || '');

            $('#modalClienteLabel').text('Editar Cliente');
            $('#previewFotoCliente').attr('src', c.foto_url);

            bootstrap.Modal.getOrCreateInstance('#modalCliente').show();
        })
        .catch(function () {
            Mensagens.erro('Erro', 'Não foi possível carregar os dados do cliente.');
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

    fetch('scripts/clientes/excluir.php', {
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
    const botao = document.getElementById('btnSalvarCliente');
    const textoOriginal = botao.textContent;
    botao.disabled = true;
    botao.textContent = 'Salvando...';

    fetch('scripts/clientes/salvar.php', {
        method: 'POST',
        body: new FormData(form)
    })
        .then(function (resposta) { return resposta.json(); })
        .then(function (dados) {
            if (dados.ok) {
                bootstrap.Modal.getOrCreateInstance('#modalCliente').hide();
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
