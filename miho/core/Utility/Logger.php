<?php

namespace Miho\Core\Utility;

use RuntimeException;
use Throwable;

class Logger
{
    private static $logfile = 'app.log';
    private static $options = [
        'logFormat' => 'Y-m-d H:i:s'
    ];

    public static function log($message)
    {
        return self::write([
            'message' => $message,
        ]);
    }

    public static function error($error)
    {

        $error = is_array($error) ? implode(' ', $error) : $error;

        $payload = [
            'message' => $error,
            'severity' => 'ERROR'
        ];

        if ($error instanceof Throwable) {
            $payload['message'] = $error->getMessage();
            $payload['context'] = $error->getTraceAsString();
        }

        return self::write($payload);
    }

    public static function warn($message)
    {
        return self::write([
            'message' => $message,
            'severity' => 'WARN'
        ]);
    }

    protected static function setup()
    {

        if (!file_exists(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }

        if (!file_exists(LOG_PATH . '/' . self::$logfile)) {
            file_put_contents(LOG_PATH . '/' . self::$logfile, 'AUTOCREATE');
        }

        if (!is_writable(LOG_PATH . '/' . self::$logfile)) {
            throw new RuntimeException('Unable to write to log!');
        }
    }

    protected static function write($args = [])
    {
        self::setup();

        $time = date(self::$options['logFormat']);
        $sev = strtoupper($args['severity'] ?? 'info');
        $message = $args['message'];
        $context = !empty($args['context']) ? ' ' . json_encode($args['context']) : '';

        if (!is_string($message)) {
            $message = json_encode($message);
        }

        $line = "{$time} {$sev}: {$message}{$context}" . PHP_EOL;

        return file_put_contents(LOG_PATH . '/' . self::$logfile, $line, FILE_APPEND);
    }
}
