<?php
include '../../php/docente_session.php';
require_once '../../php/google_calendar_config.php';

$reservationId = (int) ($_SESSION['google_calendar_reservation_id'] ?? 0);
$userId = (int) ($_SESSION['id_usuario'] ?? 0);
$config = googleCalendarConfig();

if ($reservationId <= 0 || $userId <= 0) {
    header('Location: mis_reservas.php?calendar=invalid_request');
    exit;
}

if (!googleCalendarIsConfigured($config)) {
    unset($_SESSION['google_calendar_reservation_id']);
    header('Location: update_spaces_docente.php?id=' . urlencode((string) ($_SESSION['google_calendar_space_id'] ?? '')) . '&calendar=not_configured');
    exit;
}

$state = bin2hex(random_bytes(32));
$_SESSION['google_calendar_oauth_state'] = $state;

$params = [
    'client_id' => $config['client_id'],
    'redirect_uri' => $config['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'https://www.googleapis.com/auth/calendar.events',
    'access_type' => 'offline',
    'prompt' => 'consent',
    'state' => $state,
];

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit;
