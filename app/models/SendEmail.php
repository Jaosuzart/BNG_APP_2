<?php

namespace bng\Models;

use PHPMailer\PHPMailer\PHPMailer;

class SendEmail
{
    public function send_reset_code(string $email_destinatario, int $codigo): array
    {
        // 1) Autoload do Composer (precisa existir)
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            return [
                'status' => false,
                'error'  => 'Autoload do Composer não encontrado em: ' . $autoloadPath,
            ];
        }
        require_once $autoloadPath;

        // 2) Lê configs do .env (prioriza $_ENV, depois getenv)
        $fromEmail = trim($_ENV['EMAIL_USERNAME'] ?? (getenv('EMAIL_USERNAME') ?: ''));
        $fromPass  = trim($_ENV['EMAIL_PASSWORD'] ?? (getenv('EMAIL_PASSWORD') ?: ''));
        $smtpHost  = trim($_ENV['EMAIL_HOST'] ?? (getenv('EMAIL_HOST') ?: 'smtp.gmail.com'));
        $smtpPort  = (int) trim($_ENV['EMAIL_PORT'] ?? (getenv('EMAIL_PORT') ?: '587'));

        // 3) Validações (sem warnings)
        if ($fromEmail === '' || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'error'  => 'EMAIL_USERNAME inválido no .env (valor lido: "' . $fromEmail . '")',
            ];
        }

        if ($fromPass === '') {
            return [
                'status' => false,
                'error'  => 'EMAIL_PASSWORD vazio no .env',
            ];
        }

        if ($email_destinatario === '' || !filter_var($email_destinatario, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'error'  => 'Email destinatário inválido.',
            ];
        }

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $fromEmail;
            $mail->Password   = $fromPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpPort;

            $mail->setFrom($fromEmail, 'BNG Support');
            $mail->addAddress($email_destinatario);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperação de Password - BNG';
            $mail->Body    = "<p>Seu código: <b>{$codigo}</b></p>";

            $mail->send();

            // ✅ Sempre retorna status boolean + error (null)
            return ['status' => true, 'error' => null];

        } catch (\Throwable $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}
