<?php
namespace Backend\Services;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../templates/admin_lecturer_request_email_template.php';
require_once __DIR__ . '/../templates/lecturer_request_status_email_template.php';

use Backend\Templates\AdminLecturerRequestEmailTemplate;
use Backend\Templates\LecturerRequestStatusEmailTemplate;
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

class EmailNotificationService {
    private $config;

    public function __construct() {
        if (empty($_ENV)) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->safeLoad();
        }

        $this->config = [
            'host' => $_ENV['SMTP_HOST'] ?? '',
            'port' => (int)($_ENV['SMTP_PORT'] ?? 587),
            'username' => $_ENV['SMTP_USERNAME'] ?? '',
            'password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? '',
            'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'Laboratory Schedule System',
            'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
            'smtp_auth' => filter_var($_ENV['SMTP_AUTH'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        ];
    }

    public function notifyAdminsAboutLecturerRequest($requestData, $adminRecipients) {
        if (!$this->isConfigured() || empty($adminRecipients)) {
            return ['sent' => false, 'skipped' => true];
        }

        $template = new AdminLecturerRequestEmailTemplate();
        $content = $template->build($requestData);

        return $this->sendEmail($adminRecipients, $content['subject'], $content['html'], $content['text']);
    }

    public function notifyLecturerAboutRequestStatus($requestData, $lecturerRecipient) {
        if (!$this->isConfigured() || empty($lecturerRecipient['email'])) {
            return ['sent' => false, 'skipped' => true];
        }

        $template = new LecturerRequestStatusEmailTemplate();
        $content = $template->build($requestData);

        return $this->sendEmail([$lecturerRecipient], $content['subject'], $content['html'], $content['text']);
    }

    private function isConfigured() {
        return $this->config['host'] !== '' && $this->config['from_email'] !== '';
    }

    private function sendEmail($recipients, $subject, $htmlBody, $textBody) {
        try {
            $mailer = new PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host = $this->config['host'];
            $mailer->Port = $this->config['port'];
            $mailer->SMTPAuth = $this->config['smtp_auth'];

            if ($this->config['smtp_auth']) {
                $mailer->Username = $this->config['username'];
                $mailer->Password = $this->config['password'];
            }

            if ($this->config['encryption'] !== '') {
                $mailer->SMTPSecure = $this->config['encryption'];
            }

            $mailer->setFrom($this->config['from_email'], $this->config['from_name']);

            foreach ($recipients as $recipient) {
                if (empty($recipient['email'])) {
                    continue;
                }

                $mailer->addAddress($recipient['email'], $recipient['name'] ?? '');
            }

            if (count($mailer->getToAddresses()) === 0) {
                return ['sent' => false, 'skipped' => true];
            }

            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;
            $mailer->AltBody = $textBody;
            $mailer->send();

            return ['sent' => true, 'skipped' => false];
        } catch (MailException $exception) {
            return [
                'sent' => false,
                'skipped' => false,
                'error' => $exception->getMessage(),
            ];
        }
    }
}
?>
