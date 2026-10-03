<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\SettingModel;

class NotificationService
{
    /**
     * Send email and telegram notifications for new complete lead
     */
    public static function notifyNewLead(array $lead): void
    {
        self::sendEmailNotification($lead);
        self::sendTelegramNotification($lead);
    }

    private static function sendEmailNotification(array $lead): void
    {
        $settings = SettingModel::getAll();
        $smtpHost = env('SMTP_HOST', $settings['smtp_host'] ?? '');
        $smtpUser = env('SMTP_USER', $settings['smtp_user'] ?? '');
        $smtpPass = env('SMTP_PASS', $settings['smtp_pass'] ?? '');

        if (empty($smtpHost) || empty($smtpUser)) {
            return; // SMTP not configured
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)env('SMTP_PORT', 587);

            $mail->setFrom(env('SMTP_FROM_EMAIL', 'rancangbangunkreasi.official@gmail.com'), env('SMTP_FROM_NAME', 'RBK Official'));
            $mail->addAddress(env('SMTP_TO_EMAIL', 'rancangbangunkreasi.official@gmail.com'));

            $mail->isHTML(true);
            $mail->Subject = "[LEAD BARU] {$lead['code']} - {$lead['name']} ({$lead['need']})";
            $mail->Body    = "
                <h2>Lead Baru Diterima!</h2>
                <p><strong>Kode Lead:</strong> {$lead['code']}</p>
                <p><strong>Nama:</strong> {$lead['name']}</p>
                <p><strong>WhatsApp:</strong> {$lead['phone']}</p>
                <p><strong>Kebutuhan:</strong> {$lead['need']}</p>
                <p><strong>Lokasi:</strong> {$lead['location']}</p>
                <p><strong>Luas Bangunan:</strong> {$lead['building_size_m2']} m²</p>
                <p><strong>Budget:</strong> {$lead['budget_range']}</p>
                <p><a href='" . env('APP_URL') . "/admin/leads/detail/{$lead['id']}'>Lihat di Dashboard Admin</a></p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Exception: " . $e->getMessage());
        }
    }

    private static function sendTelegramNotification(array $lead): void
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId   = env('TELEGRAM_CHAT_ID');

        if (empty($botToken) || empty($chatId)) {
            return;
        }

        $msg = "🚨 *LEAD BARU MASUK!*\n\n"
             . "• Kode: `{$lead['code']}`\n"
             . "• Nama: {$lead['name']}\n"
             . "• WA: {$lead['phone']}\n"
             . "• Layanan: {$lead['need']}\n"
             . "• Lokasi: {$lead['location']}\n"
             . "• Budget: {$lead['budget_range']}\n\n"
             . "[Buka Dashboard Admin](" . env('APP_URL') . "/admin/leads/detail/{$lead['id']})";

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $data = [
            'chat_id'    => $chatId,
            'text'       => $msg,
            'parse_mode' => 'Markdown'
        ];

        @file_get_contents($url . '?' . http_build_query($data));
    }
}
