<?php
/**
 * painel/apis/texto_whatsapp.php
 * Envio de mensagem de WhatsApp via API do Menuia.
 * Depende de $api_whatsapp e $token_whatsapp, carregados em conexao.php.
 *
 * A API do Menuia não tem endpoint direto de "enviar mensagem" pro canal
 * WhatsApp (V1/V2) — o fluxo é em 2 passos, via Inbox:
 *   1) /inbox/start-conversation -> abre/reaproveita uma conversa, devolve um ticket
 *   2) /inbox/ticket/{id}/message -> envia o texto de fato pro ticket criado
 */
require_once __DIR__ . '/../../conexao.php';

if ($api_whatsapp == 'menuia') {

    $numero   = '351927228925'; // Lisboa, PT — formato internacional, sem "+"
    $mensagem = 'Olá, esta é uma mensagem de teste!';

    // ===== PASSO 1: iniciar (ou reaproveitar) a conversa no Inbox =====
    $urlConversa = "https://api-ia.menuia.com/api/v1/inbox/start-conversation";

    $chConversa = curl_init($urlConversa);
    curl_setopt($chConversa, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chConversa, CURLOPT_POST, true);
    curl_setopt($chConversa, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token_whatsapp,
    ]);
    curl_setopt($chConversa, CURLOPT_POSTFIELDS, json_encode([
        "phone"    => $numero,
        "name"     => "Cliente",
        "channel"  => "whatsapp_v2",   // canal da conexão (Menuia -> Canais)
        "deviceId" => $device_whatsapp, // ID da conexão específica, senão a API não sabe rotear ("Configuração não encontrada")
    ]));

    $respostaConversa = curl_exec($chConversa);
    $erroConversa     = curl_error($chConversa);
    curl_close($chConversa);

    if ($erroConversa) {
        echo "Erro ao iniciar conversa: " . $erroConversa;
        exit;
    }

    $conversa = json_decode($respostaConversa, true);
    $ticketId = $conversa['id'] ?? null;

    if (!$ticketId) {
        echo "Não foi possível obter o ID do ticket. Resposta da API:<br>";
        echo $respostaConversa;
        exit;
    }

    // ===== PASSO 2: enviar a mensagem de fato pro ticket criado/reaproveitado =====
    $urlMensagem = "https://api-ia.menuia.com/api/v1/inbox/ticket/{$ticketId}/message";

    $chMensagem = curl_init($urlMensagem);
    curl_setopt($chMensagem, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chMensagem, CURLOPT_POST, true);
    curl_setopt($chMensagem, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token_whatsapp,
    ]);
    curl_setopt($chMensagem, CURLOPT_POSTFIELDS, json_encode([
        "content" => $mensagem,
    ]));

    $respostaMensagem = curl_exec($chMensagem);
    $erroMensagem     = curl_error($chMensagem);
    curl_close($chMensagem);

    if ($erroMensagem) {
        echo "Erro ao enviar mensagem: " . $erroMensagem;
        exit;
    }

    echo "Ticket: {$ticketId}<br>";
    echo "Resposta do envio da mensagem:<br>";
    echo $respostaMensagem;
}
?>
