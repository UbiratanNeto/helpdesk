<?php
// Configura o fuso horário para bater exatamente com o seu banco de dados
date_default_timezone_set('Europe/Lisbon');

// Sobe um nível para achar a conexão na raiz
require_once __DIR__ . '/../conexao.php'; 

// Registra o início da limpeza para fins de log, caso execute manualmente
$data_limite = date('Y-m-d H:i:s', strtotime('-1 day'));

try {
    // Deleta tokens marcados como usados OU tokens que expiraram há mais de 24 horas
    $stmt = $pdo->prepare("
        DELETE FROM recuperacao_senha 
        WHERE usado = 1 
           OR expira_em < :data_limite
    ");
    
    $stmt->execute([':data_limite' => $data_limite]);
    $linhas_afetadas = $stmt->rowCount();

    // Se rodar o script pelo navegador ou terminal, mostra o resultado
    echo json_encode([
        'success' => true,
        'message' => "Limpeza concluída com sucesso! Registros deletados: $linhas_afetadas."
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => "Erro ao limpar tokens: " . $e->getMessage()
    ]);
}