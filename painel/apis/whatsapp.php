<?php
/**
 * painel/apis/whatsapp.php
 * Função reutilizável de envio de mensagem de WhatsApp via API do Menuia.
 * Depende de $api_whatsapp e $token_whatsapp, carregados em conexao.php.
 *
 * Fluxo real da API (2 passos — confirmado testando em produção, ver painel/apis/texto_whatsapp.php):
 *   1) POST /inbox/start-conversation -> abre/reaproveita uma conversa, devolve um ticket
 *   2) POST /inbox/ticket/{id}/message -> envia o texto de fato pro ticket criado
 */
require_once __DIR__ . '/../../conexao.php';

/**
 * Envia uma mensagem de WhatsApp usando o provedor configurado no sistema.
 *
 * @param string $numero   Número no formato internacional, só dígitos (ex.: 5531999999999)
 * @param string $mensagem Texto da mensagem
 * @return array{sucesso: bool, erro?: string, ticketId?: string}
 */
function enviarWhatsapp(string $numero, string $mensagem): array
{
    global $api_whatsapp, $token_whatsapp, $device_whatsapp;

    if (empty($token_whatsapp) || $api_whatsapp !== 'menuia') {
        return ['sucesso' => false, 'erro' => 'Nenhuma API de WhatsApp configurada.'];
    }
    if (empty($device_whatsapp)) {
        return ['sucesso' => false, 'erro' => 'Nenhuma conexão (deviceId) de WhatsApp configurada.'];
    }

    // ===== PASSO 1: iniciar (ou reaproveitar) a conversa no Inbox =====
    // channel/deviceId identificam a conexão WhatsApp V2 específica (ver Menuia -> Canais).
    // Sem isso a API cria o ticket normalmente, mas a entrega falha com "Configuração não encontrada".
    $chConversa = curl_init("https://api-ia.menuia.com/api/v1/inbox/start-conversation");
    curl_setopt($chConversa, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chConversa, CURLOPT_POST, true);
    curl_setopt($chConversa, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token_whatsapp,
    ]);
    curl_setopt($chConversa, CURLOPT_POSTFIELDS, json_encode([
        "phone"    => $numero,
        "name"     => "Cliente",
        "channel"  => "whatsapp_v2",
        "deviceId" => $device_whatsapp,
    ]));

    $respostaConversa = curl_exec($chConversa);
    $erroConversa     = curl_error($chConversa);
    curl_close($chConversa);

    if ($erroConversa) {
        return ['sucesso' => false, 'erro' => $erroConversa];
    }

    $conversa = json_decode($respostaConversa, true);
    $ticketId = $conversa['id'] ?? null;

    if (!$ticketId) {
        return ['sucesso' => false, 'erro' => $conversa['message'] ?? 'Não foi possível abrir a conversa.'];
    }

    // ===== PASSO 2: enviar a mensagem de fato pro ticket criado/reaproveitado =====
    $chMensagem = curl_init("https://api-ia.menuia.com/api/v1/inbox/ticket/{$ticketId}/message");
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
        return ['sucesso' => false, 'erro' => $erroMensagem, 'ticketId' => $ticketId];
    }

    $dadosMensagem = json_decode($respostaMensagem, true);

    // A API pode responder HTTP 201 (mensagem registrada no ticket) mesmo quando a
    // ENTREGA de verdade pro WhatsApp falha — isso vem nos campos deliveryStatus/deliveryError,
    // não em statusCode. Sem checar isso, um "Configuração não encontrada" passaria como sucesso.
    if (isset($dadosMensagem['statusCode']) && (int) $dadosMensagem['statusCode'] >= 400) {
        return [
            'sucesso'  => false,
            'erro'     => $dadosMensagem['message'] ?? 'Falha ao enviar a mensagem.',
            'ticketId' => $ticketId,
        ];
    }
    if (($dadosMensagem['deliveryStatus'] ?? null) === 'failed') {
        return [
            'sucesso'  => false,
            'erro'     => $dadosMensagem['deliveryError'] ?? 'Falha na entrega da mensagem.',
            'ticketId' => $ticketId,
        ];
    }

    return ['sucesso' => true, 'ticketId' => $ticketId];
}
