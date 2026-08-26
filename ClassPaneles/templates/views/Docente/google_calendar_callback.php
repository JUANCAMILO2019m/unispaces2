<?php
include '../../php/docente_session.php';
require_once '../../php/google_calendar_config.php';

function calendarRedirect(string $status): void
{
    $spaceId = (int) ($_SESSION['google_calendar_space_id'] ?? 0);
    unset($_SESSION['google_calendar_reservation_id'], $_SESSION['google_calendar_space_id'], $_SESSION['google_calendar_oauth_state']);
    header('Location: update_spaces_docente.php?id=' . $spaceId . '&calendar=' . urlencode($status));
    exit;
}

if (isset($_GET['error'])) {
    calendarRedirect('cancelled');
}

$state = (string) ($_GET['state'] ?? '');
$expectedState = (string) ($_SESSION['google_calendar_oauth_state'] ?? '');
$code = (string) ($_GET['code'] ?? '');
$reservationId = (int) ($_SESSION['google_calendar_reservation_id'] ?? 0);
$userId = (int) ($_SESSION['id_usuario'] ?? 0);
$config = googleCalendarConfig();

if ($code === '' || $reservationId <= 0 || $userId <= 0 || !hash_equals($expectedState, $state) || !googleCalendarIsConfigured($config) || !function_exists('curl_init')) {
    calendarRedirect('authorization_error');
}

function googleCalendarRequest(string $url, array $options): array
{
    $curl = curl_init($url);
    curl_setopt_array($curl, $options + [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return [$status, is_string($body) ? json_decode($body, true) : null];
}

[$tokenStatus, $token] = googleCalendarRequest('https://oauth2.googleapis.com/token', [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'code' => $code,
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'redirect_uri' => $config['redirect_uri'],
        'grant_type' => 'authorization_code',
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);

if ($tokenStatus !== 200 || empty($token['access_token'])) {
    calendarRedirect('token_error');
}

$stmt = mysqli_prepare($conexion, 'SELECT r.id, r.fecha_inicio, r.fecha_final, r.tipo_reservacion, r.descripcion, e.codigo, b.nombre AS edificio FROM reservaciones r INNER JOIN espacios_academicos e ON e.id = r.id_espacio INNER JOIN edificios b ON b.id = e.edificio_id WHERE r.id = ? AND r.id_usuario = ?');
mysqli_stmt_bind_param($stmt, 'ii', $reservationId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reservation = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$reservation) {
    calendarRedirect('reservation_not_found');
}

$timeZone = new DateTimeZone('America/Bogota');
$start = new DateTime($reservation['fecha_inicio'], $timeZone);
$end = new DateTime($reservation['fecha_final'], $timeZone);

$event = [
    'summary' => 'Solicitud pendiente: ' . $reservation['tipo_reservacion'] . ' - ' . $reservation['codigo'],
    'location' => $reservation['edificio'] . ' · Espacio ' . $reservation['codigo'],
    'description' => "Reserva solicitada desde ClassTrack. Aún requiere aprobación administrativa.\n\n" . $reservation['descripcion'],
    'start' => ['dateTime' => $start->format(DATE_RFC3339), 'timeZone' => 'America/Bogota'],
    'end' => ['dateTime' => $end->format(DATE_RFC3339), 'timeZone' => 'America/Bogota'],
];

[$eventStatus] = googleCalendarRequest('https://www.googleapis.com/calendar/v3/calendars/primary/events', [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($event, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token['access_token'], 'Content-Type: application/json'],
]);

calendarRedirect($eventStatus >= 200 && $eventStatus < 300 ? 'created' : 'event_error');
