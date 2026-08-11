<?php
/**
 * painel/includes/permissoes.php
 * Checagem de acesso por cargo (RBAC simples: permissão fica no cargo, não no usuário).
 * Um cargo com acesso_total = 1 vê tudo, sem precisar de linha em cargo_permissoes.
 */

/**
 * Devolve as chaves (slugs) de painel/includes/menus.php que o cargo pode acessar.
 * Cargo com acesso_total = 1 recebe todas as páginas do catálogo automaticamente.
 *
 * @return string[] lista de slugs permitidos (ex.: ['dashboard', 'chamados'])
 */
function menusPermitidos(PDO $pdo, ?int $cargoId): array {
    $catalogo = require __DIR__ . '/menus.php';
    $todasPaginas = array_keys($catalogo['paginas']);

    if (!$cargoId) {
        return []; // Sem cargo vinculado (dado legado) = sem acesso a nada até ser corrigido
    }

    $stmt = $pdo->prepare("SELECT acesso_total FROM cargos WHERE id = ?");
    $stmt->execute([$cargoId]);
    $acessoTotal = $stmt->fetchColumn();

    if ($acessoTotal) {
        return $todasPaginas;
    }

    $stmt = $pdo->prepare("SELECT menu FROM cargo_permissoes WHERE cargo_id = ?");
    $stmt->execute([$cargoId]);
    $permitidos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Filtra contra o catálogo real: uma permissão salva pra uma página que não existe
    // mais no array não deve "vazar" acesso a nada.
    return array_values(array_intersect($permitidos, $todasPaginas));
}

/**
 * Confere se o cargo pode acessar uma página específica.
 */
function podeAcessarPagina(PDO $pdo, ?int $cargoId, string $pagina): bool {
    return in_array($pagina, menusPermitidos($pdo, $cargoId), true);
}
