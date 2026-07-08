<?php
// Evita acessos diretos ao arquivo de funções por segurança
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Importa as classes do PHPMailer para o escopo global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Altere o caminho abaixo para onde os arquivos do seu PHPMailer estão instalados de fato no seu curso.
// Se usou Composer, costuma ser: require_once __DIR__ . '/../../vendor/autoload.php';
// Se foi manual, aponte para os arquivos Exception.php e PHPMailer.php
require_once __DIR__ . '/../../vendor/autoload.php'; 

/**
 * Função global para disparo de e-mails usando PHPMailer
 * * @param string $para Endereço de e-mail do destinatário
 * @param string $assunto Assunto do e-mail
 * @param string $mensagem Corpo do e-mail (Aceita HTML)
 * @return bool Retorna true se enviou com sucesso ou false se falhou
 */
function enviarEmailGlobal($para, $assunto, $mensagem) {
    $mail = new PHPMailer(true);

    try {
        // --- CONFIGURAÇÕES DO SERVIDOR SMTP ---
        // Altere com as credenciais do seu provedor de e-mail (ex: Gmail, Outlook, Hostgator, Mailtrap)
        $mail->isSMTP();
        $mail->Host       = 'seu-servidor-smtp.com';          // Servidor SMTP
        $mail->SMTPAuth   = true;                             // Habilita autenticação SMTP
        $mail->Username   = 'seu-email@dominio.com';          // Usuário SMTP
        $mail->Password   = 'sua-senha-secreta';              // Senha SMTP ou Senha de App
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Habilita criptografia TLS (ou ENCRYPTION_SMTPS para SSL)
        $mail->Port       = 587;                              // Porta TCP (587 para TLS / 465 para SSL)
        $mail->CharSet    = 'UTF-8';                          // Evita problemas com acentuação brasileira

        // --- DESTINATÁRIOS ---
        $mail->setFrom('seu-email@dominio.com', 'Sistema HelpDesk');
        $mail->addAddress($para);

        // --- CONTEÚDO DO E-MAIL ---
        $mail->isHTML(true);                                  // Define o formato do e-mail como HTML
        $mail->Subject = $assunto;
        $mail->Body    = $mensagem;
        
        // Texto alternativo limpo caso o cliente de e-mail do usuário não suporte HTML
        $mail->AltBody = strip_tags($mensagem);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Em ambiente de produção, você pode logar o erro: error_log($mail->ErrorInfo);
        return false;
    }
}