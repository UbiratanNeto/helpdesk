<?php
/**
 * Arquivo de configuração e conexão do sistema Helpdesk
 * Adaptado para ambiente local XAMPP (Padrão do Curso)
 */

$modo_teste = 'Não';

// ===== FUSO HORÁRIO (LISBOA) =====
date_default_timezone_set('Europe/Lisbon'); // Ajuste para o fuso horário de Lisboa, Portugal

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

// ==========================================================================
// 🎨 CORES DO TEMA (Fallback padrão caso não exista configuração salva)
// ==========================================================================
$cor_primaria   = '#4f46e5'; // Roxo Indigo do curso do Hugo
$cor_secundaria = '#818cf8'; // Roxo Claro do curso do Hugo
$cor_fundo      = '#f8fafc'; // Cor sólida limpa (evita quebra de CSS ao injetar variável de cor no background)

// ===== CARREGAR E SINCRONIZAR CONFIGURAÇÃO (com cache em sessão) =====
// conexao.php é incluído em TODA página do sistema (login, painel, recuperação de senha).
// Sem cache, isso significa consultar a tabela 'config' a cada requisição, mesmo que ela
// quase nunca mude. Guardamos o resultado em $_SESSION['config'] e só voltamos ao banco
// quando ainda não existe cache para esta sessão (ex.: logo após o login).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['config']) || ($_SESSION['config_empresa'] ?? null) !== $id_empresa) {
    try {
        // Busca a configuração existente para a empresa 0
        $stmt = $pdo->query("SELECT * FROM config WHERE empresa = 0 LIMIT 1");
        $config = $stmt->fetch();

        if (!$config) {
            // Se não existir, cria o primeiro registro com as cores definidas acima
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

            // Recarrega para guardar no cache o registro recém-criado (com id, timestamps, etc.)
            $stmt = $pdo->query("SELECT * FROM config WHERE empresa = 0 LIMIT 1");
            $config = $stmt->fetch();
        } elseif ($config['cor_primaria'] !== $cor_primaria || $config['cor_secundaria'] !== $cor_secundaria) {
            // Se já existir, mas as cores estáticas no PHP forem diferentes das do Banco, nós atualizamos o banco!
            $stmt = $pdo->prepare("
                UPDATE config
                SET cor_primaria = :cor_primaria, cor_secundaria = :cor_secundaria
                WHERE empresa = 0
            ");
            $stmt->execute([
                ':cor_primaria'   => $cor_primaria,
                ':cor_secundaria' => $cor_secundaria
            ]);
            $config['cor_primaria']   = $cor_primaria;
            $config['cor_secundaria'] = $cor_secundaria;
        }

        $_SESSION['config']         = $config ?: [];
        $_SESSION['config_empresa'] = $id_empresa;
    } catch (PDOException $e) {
        // Se a tabela 'config' não existir ainda, ignora e usa as variáveis padrão do PHP
        $_SESSION['config']         = [];
        $_SESSION['config_empresa'] = $id_empresa;
    }
}

// Usa o config já cacheado na sessão (evita repetir a consulta nas próximas páginas)
$config = $_SESSION['config'];

if ($config) {
    // AJUSTE CRÍTICO: Se já existem cores no banco, garante que as variáveis PHP do tema usem as cores do Banco de Dados!
    $cor_primaria     = $config['cor_primaria']     ?? $cor_primaria;
    $cor_secundaria   = $config['cor_secundaria']   ?? $cor_secundaria;

    // Carrega as demais informações salvas no banco
    $nome_sistema     = $config['nome_sistema']     ?? $nome_sistema;
    $telefone_sistema = $config['telefone_sistema'] ?? $telefone_sistema;
    $email_sistema    = $config['email_sistema']    ?? $email_sistema;
    $id_empresa       = (int) ($config['empresa']   ?? $id_empresa);

    // Dados de SMTP: host/porta/segurança podem ir pro formulário de configurações normalmente.
    // $smtp_senha NUNCA deve ser impresso em HTML — existe aqui só pra uso futuro no envio de e-mails.
    $smtp_host      = $config['smtp_host']      ?? '';
    $smtp_porta     = $config['smtp_porta']     ?? '';
    $smtp_seguranca = $config['smtp_seguranca'] ?? '';
    $smtp_senha     = $config['smtp_senha']     ?? '';
}
?>