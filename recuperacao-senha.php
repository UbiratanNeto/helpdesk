<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Define o fuso horário correto para a verificação de expiração no PHP
date_default_timezone_set('Europe/Lisbon');

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/conexao.php';

// Garante acesso às variáveis de estilo dinâmico vindas da conexao.php
global $cor_primaria, $cor_secundaria;

// Se as variáveis estiverem vazias, define as cores roxas padrão para o layout
$primary = (!empty($cor_primaria)) ? $cor_primaria : '#4f46e5';
$secondary = (!empty($cor_secundaria)) ? $cor_secundaria : '#818cf8';

$token = filter_input(INPUT_GET, 'token', FILTER_DEFAULT);
if ($token) {
    $token = trim($token);
}

$isValidToken = false;
$errorMessage = '';

if ($token) {
    try {
        // Consulta na tabela em português 'recuperacao_senha' e 'usuarios'
        $stmt = $pdo->prepare("
            SELECT r.id, r.expira_em 
            FROM recuperacao_senha r
            INNER JOIN usuarios u ON r.usuario_id = u.id
            WHERE r.token = :token 
              AND r.usado = 0 
              AND u.ativo = 1
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        $recovery = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($recovery) {
            $currentTime = time(); 
            $expiresAt = strtotime($recovery['expira_em']); 

            // Valida se o token não expirou
            if ($currentTime <= $expiresAt) {
                $isValidToken = true;
            } else {
                $errorMessage = 'Este link de recuperação expirou (validade de 30 minutos).';
            }
        } else {
            $errorMessage = 'Este link de recuperação é inválido ou já foi utilizado.';
        }
    } catch (PDOException $e) {
        $errorMessage = 'Ocorreu um erro no sistema. Por favor, tente novamente mais tarde.';
    }
} else {
    $errorMessage = 'O token de segurança está ausente.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha - HelpDesk</title>
    
    <!-- Tailwind CSS para estrutura rápida -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- SweetAlert2 para notificações bonitas -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- CSS Unificado (Opcional se precisar de herança global) -->
    <link rel="stylesheet" href="css/main.css">

    <!-- Injeção dinâmica de variáveis CSS para cor primária e secundária -->
    <style>
        :root {
            --cor-primaria: <?php echo $primary; ?>;
            --cor-secundaria: <?php echo $secondary; ?>;
        }

        /* Substitui o fundo hardcoded pelo gradiente dinâmico do sistema */
        body {
            background: linear-gradient(135deg, var(--cor-primaria) 0%, var(--cor-secundaria) 100%) !important;
        }

        /* Classe utilitária para aplicar nossa cor de marca aos elementos do formulário */
        .btn-brand {
            background-color: var(--cor-primaria) !important;
            transition: filter 0.2s ease;
        }
        .btn-brand:hover {
            filter: brightness(0.9);
        }
        .focus-brand:focus {
            --tw-ring-color: var(--cor-primaria) !important;
            border-color: var(--cor-primaria) !important;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md m-4">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Redefinir senha</h2>
            <p class="text-sm text-gray-600">Insira a sua nova senha de acesso abaixo</p>
        </div>

        <?php if (!$isValidToken): ?>
            <div class="text-center p-4 bg-red-50 rounded-md border border-red-200">
                <p class="text-red-700 font-medium mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
                <a href="index.php" class="inline-block w-full btn-brand text-white font-semibold py-2 px-4 rounded text-center text-sm transition">
                    Voltar para o Login
                </a>
            </div>
        <?php else: ?>
            <form id="formResetPassword" class="space-y-4">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
                    <input type="password" name="nova_senha" required minlength="6" placeholder="Mínimo 6 caracteres"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus-brand text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
                    <input type="password" id="confirm_password" required minlength="6" placeholder="Repita a senha"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus-brand text-sm">
                </div>
                <button type="submit" id="btnSubmit"
                    class="w-full btn-brand text-white font-semibold py-2 px-4 rounded transition text-sm flex justify-center items-center cursor-pointer">
                    Atualizar senha
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formResetPassword');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const password = form.querySelector('input[name="nova_senha"]').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const btn = document.getElementById('btnSubmit');

                // Puxa a cor do PHP para manter o botão do SweetAlert no tom correto
                const primaryColor = '<?php echo $primary; ?>';

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Aviso',
                        text: 'As senhas não coincidem.',
                        confirmButtonColor: primaryColor
                    });
                    return;
                }

                btn.disabled = true;
                btn.innerText = 'Atualizando...';

                const formData = new FormData(form);

                fetch('scripts/atualizar-senha.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: data.message,
                                confirmButtonText: 'Ir para o Login',
                                confirmButtonColor: primaryColor,
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) window.location.href = 'index.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.message,
                                confirmButtonColor: primaryColor
                            });
                            btn.disabled = false;
                            btn.innerText = 'Atualizar senha';
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de Sistema',
                            text: 'Não foi possível conectar ao servidor.',
                            confirmButtonColor: primaryColor
                        });
                        btn.disabled = false;
                        btn.innerText = 'Atualizar senha';
                    });
            });
        });
    </script>
</body>

</html>