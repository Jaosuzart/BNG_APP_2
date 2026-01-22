<?php

namespace bng\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendEmail
{
    public function send_reset_code($email_destinatario, $codigo)
    {
        // =============================================================
        // CORREÇÃO DOS CAMINHOS (Aponta para a pasta vendor)
        // =============================================================
        
        // __DIR__ pega a pasta atual (app/models). 
        // Subimos duas vezes (../../) para chegar à raiz e entrar em vendor.
        $path = __DIR__ . '/../../vendor/phpmailer/phpmailer/src/';

        // Verifica se os arquivos existem antes de carregar (para evitar erros fatais diretos)
        if (!file_exists($path . 'Exception.php')) {
            die('Erro: Não foi possível encontrar a biblioteca PHPMailer em: ' . $path);
        }

        require_once($path . 'Exception.php');
        require_once($path . 'PHPMailer.php');
        require_once($path . 'SMTP.php');

        $mail = new PHPMailer(true);

        try {
            // =============================================================
            // CONFIGURAÇÕES DO SERVIDOR
            // =============================================================
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   ='joaomarcelosuzartcastro@gmail.com';   // Seu Email
            $mail->Password   ='rywsntnkxzqhzuta
';                    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // =============================================================
            // DESTINATÁRIOS E CONTEÚDO
            // =============================================================
            $mail->setFrom('joaomarcelosuzartcastro@gmail.com', 'BNG Support');
            $mail->addAddress($email_destinatario);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperação de Password - BNG';
            
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2 style='color: #6c757d;'>Basic Name Gathering</h2>
                    <p>Recebemos um pedido para recuperar a sua password.</p>
                    <p>O seu código de verificação é:</p>
                    <div style='background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; text-align: center; border-radius: 5px; width: fit-content;'>
                        <span style='font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #0d6efd;'>$codigo</span>
                    </div>
                    <p style='margin-top: 20px; font-size: 12px; color: #999;'>Se não pediu este código, por favor ignore este email.</p>
                </div>
            ";

            $mail->send();
            return ['status' => true];

        } catch (Exception $e) {
            echo "Erro:" .$e->getMessage();
        }
    }
}