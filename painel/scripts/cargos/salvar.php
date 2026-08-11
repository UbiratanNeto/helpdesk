<?php
/**
 * painel/scripts/cargos/salvar.php
 * Cria ou atualiza um cargo (nome + acesso_total + permissões de menu).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

function resp($ok, $msg, $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra), JSON_UNESCAPED_UNICODE);
    exit();
}

if (empty($_SESSION['id'])) {
    resp(false, 'Sessão expirada.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    resp(false, 'Método inválido.');
}

require_once __DIR__ . '/../../../conexao.php';

$id           = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome         = trim($_POST['nome'] ?? '');
$acesso_total = isset($_POST['acesso_total']) ? 1 : 0;

// Só aceita, como permissão, menus que existem de verdade no catálogo — evita gravar
// lixo em cargo_permissoes vindo de um POST manipulado.
$catalogo      = require __DIR__ . '/../../includes/menus.php';
$menusValidos  = array_keys($catalogo['paginas']);
$permissoesPost = $_POST['permissoes'] ?? [];
$permissoes    = array_values(array_intersect((array) $permissoesPost, $menusValidos));

if ($nome === '') {
    resp(false, 'Informe o nome do cargo.');
}

try {
    // Nome duplicado (considerando outros cargos, não o próprio ao editar)
    $stmtDup = $pdo->prepare("SELECT id FROM cargos WHERE nome = ? AND id != ?");
    $stmtDup->execute([$nome, $id ?? 0]);
    if ($stmtDup->fetch()) {
        resp(false, 'Já existe um cargo com esse nome.');
    }

    $pdo->beginTransaction();

    if ($id) {
        $stmt = $pdo->prepare("UPDATE cargos SET nome = :nome, acesso_total = :acesso_total WHERE id = :id");
        $stmt->execute([':nome' => $nome, ':acesso_total' => $acesso_total, ':id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cargos (nome, acesso_total, empresa) VALUES (:nome, :acesso_total, :empresa)");
        $stmt->execute([':nome' => $nome, ':acesso_total' => $acesso_total, ':empresa' => $_SESSION['id_empresa'] ?? 0]);
        $id = (int) $pdo->lastInsertId();
    }

    // Ressincroniza as permissões: apaga tudo e regrava o conjunto atual enviado pelo form.
    // Mais simples e seguro contra inconsistência do que tentar calcular um diff.
    $stmtDel = $pdo->prepare("DELETE FROM cargo_permissoes WHERE cargo_id = ?");
    $stmtDel->execute([$id]);

    if (!empty($permissoes)) {
        $stmtIns = $pdo->prepare("INSERT INTO cargo_permissoes (cargo_id, menu) VALUES (:cargo_id, :menu)");
        foreach ($permissoes as $menu) {
            $stmtIns->execute([':cargo_id' => $id, ':menu' => $menu]);
        }
    }

    $pdo->commit();

    resp(true, $id ? 'Cargo salvo com sucesso!' : 'Cargo cadastrado com sucesso!');
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    resp(false, 'Erro no banco de dados: ' . $e->getMessage());
}
