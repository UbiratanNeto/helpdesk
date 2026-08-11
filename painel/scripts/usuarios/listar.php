<?php
/**
 * painel/scripts/usuarios/listar.php
 * Devolve a lista de usuários em JSON puro (sem HTML embutido) — o front-end
 * (painel/assets/js/usuarios.js) monta as linhas da tabela com DOM/textContent,
 * o que evita qualquer risco de HTML malicioso vindo de um nome/e-mail salvo no banco.
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

require_once __DIR__ . '/../../../conexao.php';

try {
    $stmt = $pdo->query("SELECT id, nome, email, telefone, foto, nivel, ativo FROM usuarios ORDER BY id DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($usuarios as $user) {
        $fotoExiste = !empty($user['foto']) && file_exists(__DIR__ . '/../../../uploads/perfil/' . $user['foto']);

        $data[] = [
            'id'        => (int) $user['id'],
            'nome'      => $user['nome'],
            'email'     => $user['email'],
            'telefone'  => $user['telefone'],
            'nivel'     => $user['nivel'],
            'ativo'     => (int) $user['ativo'],
            'foto_url'  => $fotoExiste ? '../uploads/perfil/' . $user['foto'] : '../uploads/perfil/sem_foto.png',
        ];
    }

    resp(true, '', ['data' => $data]);
} catch (PDOException $e) {
    resp(false, 'Erro ao carregar usuários: ' . $e->getMessage());
}
