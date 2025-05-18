<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public static function send($to, $subject, $body): bool
    {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.example.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME', 'your-email@example.com');
            $mail->Password = env('MAIL_PASSWORD', 'your-password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('MAIL_PORT', 587);

            // Validate required settings
            if (!$mail->Host || !$mail->Username || !$mail->Password) {
                throw new Exception('Missing required SMTP configuration');
            }

            // Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'no-reply@eklase.com'), 'E-Klase');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail error: {$mail->ErrorInfo}");
            return false;
        }
    }
}