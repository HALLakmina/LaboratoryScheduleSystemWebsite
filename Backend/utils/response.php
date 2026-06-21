<?php
namespace Backend\Utils;

class Response {
    public static function send(string $status, string $message, $data = null, array $extra = []): void {
        http_response_code((int)$status);
        echo json_encode(array_merge(
            [
                'status'  => $status,
                'message' => $message,
                'data'    => $data,
            ],
            $extra
        ));
        exit;
    }

    public static function success(string $message, $data = null, string $status = '200'): void {
        self::send($status, $message, $data);
    }

    public static function error(string $status, string $message, array $extra = []): void {
        self::send($status, $message, null, $extra);
    }
}
?>
