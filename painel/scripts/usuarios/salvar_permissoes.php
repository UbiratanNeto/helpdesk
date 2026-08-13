<?php
/**
 * painel/scripts/usuarios/salvar_permissoes.php
 * Ressincroniza as permissões de menu de um usuário (Tela de Usuários > botão Permissões).
 * Usuários com cargo de acesso_total (ex.: Administrador) são bloqueados aqui de propósito —
 * essa tela não deve mexer no acesso deles, que já é irrestrito pelo cargo.
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

$usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT);
if (!$usuario_id) {
    resp(false, 'Usuário inválido.');
}

// Só aceita, como permissão, menus que existem de verdade no catálogo — evita gravar
// lixo em usuario_permissoes vindo de um POST manipulado.
$catalogo       = require __DIR__ . '/../../includes/menus.php';
$menusValidos   = array_keys($catalogo['paginas']);
$permissoesPost = $_POST['permissoes'] ?? [];
$permissoes     = array_values(array_intersect((array) $permissoesPost, $menusValidos));

try {
    $stmt = $pdo->prepare("
        SELECT c.acesso_total
        FROM usuarios u
        LEFT JOIN cargos c ON c.id = u.cargo_id
        WHERE u.id = ?
    ");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        resp(false, 'Usuário não encontrado.');
    }
    if ($usuario['acesso_total']) {
        resp(false, 'Este usuário já tem acesso total pelo cargo — não é possível configurar permissões individuais pra ele.');
    }

    $pdo->beginTransaction();

    // Ressincroniza: apaga tudo e regrava o conjunto atual enviado pelo form. Mais simples
    // e seguro contra inconsistência do que tentar calcular um diff.
    $stmtDel = $pdo->prepare("DELETE FROM usuario_permissoes WHERE usuario_id = ?");
    $stmtDel->execute([$usuario_id]);

    if (!empty($permissoes)) {
        $stmtIns = $pdo->prepare("INSERT INTO usuario_permissoes (usuario_id, menu_id) VALUES (:usuario_id, :menu_id)");
        foreach ($permissoes as $menu) {
            $stmtIns->execute([':usuario_id' => $usuario_id, ':menu_id' => $menu]);
        }
    }

    $pdo->commit();

    resp(true, 'Permissões atualizadas com sucesso!');
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    resp(false, 'Erro no banco de dados: ' . $e->getMessage());
}
