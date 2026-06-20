<?php
namespace Backend\Services;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../utils/logger.php';

use Backend\Utils\Logger;
use Dotenv\Dotenv;

class WhatsAppNotificationService {
    private $config;

    public function __construct() {
        if (empty($_ENV)) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->safeLoad();
        }

        $this->config = [
            'phone_number_id' => $_ENV['WHATSAPP_PHONE_NUMBER_ID'] ?? '',
            'access_token' => $_ENV['WHATSAPP_ACCESS_TOKEN'] ?? '',
            'api_version' => $_ENV['WHATSAPP_API_VERSION'] ?? 'v21.0',
            'default_country_code' => preg_replace('/[^0-9]/', '', (string)($_ENV['WHATSAPP_DEFAULT_COUNTRY_CODE'] ?? '')),
            'language' => $_ENV['WHATSAPP_TEMPLATE_LANGUAGE'] ?? 'en_US',
            'template_admin_new_request' => $_ENV['WHATSAPP_TEMPLATE_ADMIN_NEW_REQUEST'] ?? '',
            'template_lecturer_confirmed' => $_ENV['WHATSAPP_TEMPLATE_LECTURER_CONFIRMED'] ?? '',
            'template_lecturer_canceled' => $_ENV['WHATSAPP_TEMPLATE_LECTURER_CANCELED'] ?? '',
        ];
    }

    public function notifyAdminsAboutLecturerRequest($requestData, $adminRecipients) {
        $templateName = $this->config['template_admin_new_request'];
        if (!$this->isConfigured() || $templateName === '' || empty($adminRecipients)) {
            return ['sent' => false, 'skipped' => true];
        }

        $bodyParams = [
            (string)($requestData['lecturer_name'] ?? 'Lecturer'),
            (string)($requestData['subject'] ?? '-'),
            (string)($requestData['date'] ?? '-'),
            (string)($requestData['column_heading'] ?? '-'),
            (string)($requestData['time_slot_label'] ?? '-'),
            (string)($requestData['group_name'] ?? '-'),
        ];

        $results = [];
        foreach ($adminRecipients as $admin) {
            if (empty($admin['mobile_number'])) {
                continue;
            }
            $results[] = $this->sendTemplateMessage($admin['mobile_number'], $templateName, $bodyParams);
        }

        return ['sent' => !empty($results), 'skipped' => empty($results), 'results' => $results];
    }

    public function notifyLecturerAboutRequestStatus($requestData, $lecturerRecipient) {
        $isConfirmed = ($requestData['action'] ?? '') === 'confirmed';
        $templateName = $isConfirmed
            ? $this->config['template_lecturer_confirmed']
            : $this->config['template_lecturer_canceled'];

        if (!$this->isConfigured() || $templateName === '' || empty($lecturerRecipient['mobile_number'])) {
            return ['sent' => false, 'skipped' => true];
        }

        $bodyParams = [
            (string)($requestData['lecturer_name'] ?? 'Lecturer'),
            (string)($requestData['subject'] ?? '-'),
            (string)($requestData['date'] ?? '-'),
            (string)($requestData['column_heading'] ?? '-'),
            (string)($requestData['time_slot_label'] ?? '-'),
            $isConfirmed
                ? (string)($requestData['lab_name'] ?? 'Not assigned')
                : (string)($requestData['admin_message'] ?? '-'),
        ];

        return $this->sendTemplateMessage($lecturerRecipient['mobile_number'], $templateName, $bodyParams);
    }

    private function isConfigured() {
        return $this->config['phone_number_id'] !== '' && $this->config['access_token'] !== '';
    }

    private function sendTemplateMessage($rawNumber, $templateName, array $bodyParams) {
        $toNumber = $this->normalizeNumber($rawNumber);
        if ($toNumber === null) {
            Logger::warning('WhatsApp message skipped — invalid phone number', [
                'template' => $templateName,
                'raw_number' => $rawNumber,
            ]);
            return ['sent' => false, 'skipped' => true, 'error' => 'invalid_phone_number'];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $toNumber,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $this->config['language']],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => array_map(function ($value) {
                            return ['type' => 'text', 'text' => $value];
                        }, $bodyParams),
                    ],
                ],
            ],
        ];

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            $this->config['api_version'],
            $this->config['phone_number_id']
        );

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->config['access_token'],
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError !== '') {
            Logger::error('WhatsApp API cURL transport error', [
                'error' => $curlError,
                'template' => $templateName,
                'to' => $toNumber,
            ]);
            return ['sent' => false, 'skipped' => false, 'error' => $curlError];
        }

        $decoded = json_decode((string)$response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            Logger::error('WhatsApp API request failed', [
                'http_code' => $httpCode,
                'response' => $decoded ?? $response,
                'template' => $templateName,
                'to' => $toNumber,
            ]);
            return [
                'sent' => false,
                'skipped' => false,
                'error' => $decoded['error']['message'] ?? 'unknown_whatsapp_api_error',
            ];
        }

        return ['sent' => true, 'skipped' => false, 'message_id' => $decoded['messages'][0]['id'] ?? null];
    }

    private function normalizeNumber($rawNumber) {
        $digits = preg_replace('/[^0-9]/', '', (string)$rawNumber);
        if ($digits === '') {
            return null;
        }

        $countryCode = $this->config['default_country_code'];
        if ($countryCode !== '' && strpos($digits, $countryCode) !== 0) {
            $digits = $countryCode . ltrim($digits, '0');
        }

        return strlen($digits) >= 9 ? $digits : null;
    }
}
?>
