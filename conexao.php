<?php
/**
 * Arquivo de configuração e conexão do sistema Helpdesk
 * Contém variáveis principais do sistema e configurações de conexão
 */

$modo_teste = 'Sim';

// ===== FUSO HORÁRIO (BRASÍLIA) =====
date_default_timezone_set('America/Sao_Paulo');

// ===== CONFIGURAÇÃO DO BANCO (XAMPP) =====
$db_servidor = 'localhost';   // normalmente 'localhost' no XAMPP
$db_usuario  = 'root';        // usuário padrão do XAMPP
$db_senha    = '';            // senha padrão em instalações locais costuma ser vazia
$db_banco    = 'helpdesk';    // nome do banco de dados
$db_charset  = 'utf8mb4';

try {
    $dsn = "mysql:host={$db_servidor};dbname={$db_banco};charset={$db_charset}";
    $pdo = new PDO($dsn, $db_usuario, $db_senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Em produção, evite exibir a mensagem completa de erro
    die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
}

// ===== INFORMAÇÕES DO SISTEMA =====
$nome_sistema = 'Sistema Helpdesk';
$telefone_sistema = '(31)97527-5084';
$email_sistema = 'contato@hugocursos.com.br';
$id_empresa = 0;

// ===== CORES DO TEMA =====
// Cor primária (usada em botões, links etc.)
$cor_primaria = '#667eea';
$cor_secundaria = '#764ba2';

// Cor de fundo (gradiente azul/roxo)
$cor_fundo = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

// ===== INSERÇÃO DO PRIMEIRO REGISTRO EM CONFIG (só se a tabela estiver vazia) =====
$stmt = $pdo->query("SELECT COUNT(*) FROM config");
if ($stmt->fetchColumn() == 0) {
    $sql = "INSERT INTO config (nome_sistema, telefone_sistema, email_sistema, cor_primaria, cor_secundaria, empresa)
            VALUES (:nome_sistema, :telefone_sistema, :email_sistema, :cor_primaria, :cor_secundaria, :empresa)";
    $ins = $pdo->prepare($sql);
    $ins->execute([
        ':nome_sistema'     => $nome_sistema,
        ':telefone_sistema' => $telefone_sistema,
        ':email_sistema'    => $email_sistema,
        ':cor_primaria'     => $cor_primaria,
        ':cor_secundaria'   => $cor_secundaria,
        ':empresa'          => $id_empresa,
    ]);
}

// ===== CARREGAR CONFIG DO BANCO (sobrescreve as variáveis quando existir registro) =====
$stmt = $pdo->query("SELECT * FROM config WHERE empresa = 0 LIMIT 1");
$config = $stmt->fetch();

if ($config) {
    $nome_sistema     = $config['nome_sistema'] ?? $nome_sistema;
    $telefone_sistema = $config['telefone_sistema'] ?? $telefone_sistema;
    $email_sistema    = $config['email_sistema'] ?? $email_sistema;
    $cor_primaria    = $config['cor_primaria'] ?? $cor_primaria;
    $cor_secundaria  = $config['cor_secundaria'] ?? $cor_secundaria;
    $id_empresa      = (int) ($config['empresa'] ?? $id_empresa);
}

?>