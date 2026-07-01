<?php
/**
 * Arquivo de configuração e conexão do sistema Helpdesk
 * Adaptado para ambiente local XAMPP (Padrão do Curso)
 */

$modo_teste = 'Não';

// ===== FUSO HORÁRIO (BRASÍLIA) =====
date_default_timezone_set('America/Sao_Paulo');

// ===== CONFIGURAÇÃO DO BANCO (XAMPP) =====
// No XAMPP, o servidor padrão sempre será localhost ou 127.0.0.1
$db_servidor = 'localhost';     // AJUSTADO: Voltando para localhost (XAMPP)
$db_usuario  = 'root';          // Padrão do XAMPP é root
$db_senha    = '';              // AJUSTADO: No XAMPP a senha por padrão vem VAZIA
$db_banco    = 'helpdesk';      // AJUSTADO: O nome do banco que você criou manualmente
$db_charset  = 'utf8mb4';

try {
    $dsn = "mysql:host={$db_servidor};dbname={$db_banco};charset={$db_charset}";
    $pdo = new PDO($dsn, $db_usuario, $db_senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Interrompe o código e mostra o erro se não conseguir conectar
    die('Erro ao conectar ao banco de dados no XAMPP: ' . $e->getMessage());
}

// ===== SENHA PADRÃO (usada ao criar novos usuários no sistema) =====
$senha_padrao = '123';

// ===== ACESSO DE TESTE (preenchimento automático no login quando modo_teste = 'Sim') =====
$usuario_teste = 'contato@hugocursos.com.br';
$senha_teste = '123';

// ===== INFORMAÇÕES DO SISTEMA =====
$nome_sistema = 'Sistema Helpdesk';
$telefone_sistema = '(31)97527-5084';
$email_sistema = 'contato@hugocursos.com.br';
$id_empresa = 0;

// ===== CORES DO TEMA =====
$cor_primaria = '#667eea';
$cor_secundaria = '#764ba2';
$cor_fundo = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

// ===== CARREGAR CONFIG DO BANCO =====
// XAMPP OBS: Certifique-se de que a tabela 'config' já existe no seu banco de dados
try {
    // Carregar config da tabela (empresa = 0). Se não existir registro, insere o primeiro.
    $stmt = $pdo->query("SELECT * FROM config WHERE empresa = 0 LIMIT 1");
    $config = $stmt->fetch();

    if (!$config) {
        $stmt = $pdo->prepare("
            INSERT INTO config (nome_sistema, telefone_sistema, email_sistema, cor_primaria, cor_secundaria, empresa)
            VALUES (:nome_sistema, :telefone_sistema, :email_sistema, :cor_primaria, :cor_secundaria, :empresa)
        ");
        $stmt->execute([
            ':nome_sistema'     => $nome_sistema,
            ':telefone_sistema' => $telefone_sistema,
            ':email_sistema'    => $email_sistema,
            ':cor_primaria'     => $cor_primaria,
            ':cor_secundaria'   => $cor_secundaria,
            ':empresa'          => $id_empresa,
        ]);
    } else {
        $nome_sistema     = $config['nome_sistema'] ?? $nome_sistema;
        $telefone_sistema = $config['telefone_sistema'] ?? $telefone_sistema;
        $email_sistema    = $config['email_sistema'] ?? $email_sistema;
        $cor_primaria     = $config['cor_primaria'] ?? $cor_primaria;
        $cor_secundaria   = $config['cor_secundaria'] ?? $cor_secundaria;
        $id_empresa       = (int) ($config['empresa'] ?? $id_empresa);
    }
} catch (PDOException $e) {
    // Se a tabela 'config' não existir ainda, o PHP ignora o erro e carrega os dados padrão acima
}
?>