<?php

function googleCalendarConfig(): array
{
    $envPath = dirname(__DIR__, 3);
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';

    if (is_file($autoload)) {
        require_once $autoload;
    }

    if (class_exists('Dotenv\\Dotenv')) {
        \Dotenv\Dotenv::createImmutable($envPath)->safeLoad();
    }

    return [
        'client_id' => $_ENV['GOOGLE_CALENDAR_CLIENT_ID'] ?? getenv('GOOGLE_CALENDAR_CLIENT_ID') ?: '',
        'client_secret' => $_ENV['GOOGLE_CALENDAR_CLIENT_SECRET'] ?? getenv('GOOGLE_CALENDAR_CLIENT_SECRET') ?: '',
        'redirect_uri' => $_ENV['GOOGLE_CALENDAR_REDIRECT_URI'] ?? getenv('GOOGLE_CALENDAR_REDIRECT_URI') ?: '',
    ];
}

function googleCalendarIsConfigured(array $config): bool
{
    return $config['client_id'] !== ''
        && $config['client_secret'] !== ''
        && filter_var($config['redirect_uri'], FILTER_VALIDATE_URL) !== false;
}
