<?php
/**
 * Procesa un archivo .xlsx con horarios de cursos y crea una reservación
 * por cada semana del rango indicado que coincida con el día seleccionado.
 *
 * Requiere PhpSpreadsheet:
 *   composer require phpoffice/phpspreadsheet
 *
 * Columnas esperadas en la hoja "Horarios" (o la primera hoja):
 *   correo_usuario | id_espacio | dia_semana | hora_inicio | hora_fin
 *   fecha_inicio_rango | fecha_fin_rango | tipo_reservacion | descripcion
 *   correos_estudiantes   <-- NUEVO: uno o varios correos separados por coma
 */

// --- Blindaje: esta ruta SIEMPRE debe responder JSON, nunca HTML de error ---
error_reporting(E_ALL);
ini_set('display_errors', 0);   // no imprimir errores como HTML
ini_set('log_errors', 1);       // sí dejarlos en el log del servidor
ob_start();                     // atrapar cualquier salida accidental (warnings, notices, etc.)

function responderError(string $mensaje, int $httpCode = 200): void {
    if (ob_get_length()) { ob_clean(); } // descartar cualquier HTML/warning que se haya colado
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => $mensaje]);
    exit();
}

// Convierte errores fatales (p. ej. autoload.php no encontrado) en JSON en vez de una página en blanco/HTML
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
        responderError('Error interno del servidor: ' . $error['message'] . ' (' . $error['file'] . ':' . $error['line'] . ')');
    }
});

try {
    include '../../php/admin_session.php';
} catch (Throwable $e) {
    responderError('No se pudo iniciar sesión de administrador: ' . $e->getMessage());
}

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    responderError('No autorizado.');
}

$autoloadPath = __DIR__ . '/../../vendor/autoload.php'; // ajustar ruta al autoload de Composer
if (!file_exists($autoloadPath)) {
    responderError("No se encontró vendor/autoload.php en '$autoloadPath'. Instala PhpSpreadsheet con 'composer require phpoffice/phpspreadsheet' y/o corrige la ruta.");
}
require $autoloadPath;

if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
    responderError('PhpSpreadsheet no está instalado. Ejecuta: composer require phpoffice/phpspreadsheet');
}

try {
    include '../../php/conexion_be.php';
} catch (Throwable $e) {
    responderError('No se pudo conectar a la base de datos: ' . $e->getMessage());
}
if (!isset($conexion) || !$conexion) {
    responderError('La conexión a la base de datos no está disponible ($conexion).');
}

use PhpOffice\PhpSpreadsheet\IOFactory;

// A partir de aquí, cualquier salida accidental previa se descarta y respondemos siempre en JSON
if (ob_get_length()) { ob_clean(); }
header('Content-Type: application/json; charset=utf-8');

if (!isset($_FILES['archivo_horario']) || $_FILES['archivo_horario']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No se recibió el archivo o hubo un error al subirlo.']);
    exit();
}

$archivoTmp = $_FILES['archivo_horario']['tmp_name'];
$nombreOriginal = $_FILES['archivo_horario']['name'];
$extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

if ($extension !== 'xlsx') {
    echo json_encode(['success' => false, 'error' => 'Solo se aceptan archivos .xlsx.']);
    exit();
}

$aprobarAutomatico = isset($_POST['aprobar_automatico']) && $_POST['aprobar_automatico'] === 'aceptada';
$estadoReserva = $aprobarAutomatico ? 'aceptada' : 'pendiente';

$diasSemana = [
    'domingo' => 0, 'lunes' => 1, 'martes' => 2, 'miercoles' => 3, 'miércoles' => 3,
    'jueves' => 4, 'viernes' => 5, 'sabado' => 6, 'sábado' => 6,
];

try {
    $spreadsheet = IOFactory::load($archivoTmp);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'No se pudo leer el archivo Excel: ' . $e->getMessage()]);
    exit();
}

$sheet = $spreadsheet->getSheetByName('Horarios') ?? $spreadsheet->getActiveSheet();
$filas = $sheet->toArray(null, true, true, false); // array indexado, sin claves de columna

if (count($filas) < 2) {
    echo json_encode(['success' => false, 'error' => 'El archivo no tiene datos (verifica que la fila 1 sean encabezados).']);
    exit();
}

$encabezados = array_map(function ($h) {
    return strtolower(trim((string)$h));
}, $filas[0]);

$colIndex = array_flip($encabezados);
$columnasRequeridas = [
    'correo_usuario', 'id_espacio', 'dia_semana', 'hora_inicio', 'hora_fin',
    'fecha_inicio_rango', 'fecha_fin_rango', 'tipo_reservacion', 'descripcion',
    'correos_estudiantes' // NUEVO
];
foreach ($columnasRequeridas as $col) {
    if (!isset($colIndex[$col])) {
        echo json_encode(['success' => false, 'error' => "Falta la columna requerida: $col"]);
        exit();
    }
}

// --- Preparar consultas reutilizables (todas parametrizadas) ---
$stmtUsuario = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
$stmtEspacio = mysqli_prepare($conexion, "SELECT id, codigo FROM espacios_academicos WHERE id = ? LIMIT 1");
$stmtEstudiante = mysqli_prepare($conexion, "SELECT id FROM estudiantes WHERE correo = ? LIMIT 1"); // NUEVO
$stmtConflicto = mysqli_prepare($conexion, "
    SELECT COUNT(*) AS conflictos FROM reservaciones
    WHERE id_espacio = ?
      AND estado IN ('pendiente', 'aceptada')
      AND (
            (? BETWEEN fecha_inicio AND fecha_final) OR
            (? BETWEEN fecha_inicio AND fecha_final) OR
            (fecha_inicio BETWEEN ? AND ?) OR
            (fecha_final BETWEEN ? AND ?)
          )
      AND NOT (? = fecha_final OR ? = fecha_inicio)
");
$stmtInsertar = mysqli_prepare($conexion, "
    INSERT INTO reservaciones (id_usuario, id_espacio, fecha_inicio, fecha_final, tipo_reservacion, descripcion, estado)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmtInsertarEstudiante = mysqli_prepare($conexion, "
    INSERT INTO reservaciones_estudiantes (id_reservacion, id_estudiante) VALUES (?, ?)
"); // NUEVO

if (!$stmtUsuario || !$stmtEspacio || !$stmtEstudiante || !$stmtConflicto || !$stmtInsertar || !$stmtInsertarEstudiante) {
    responderError('Error preparando las consultas SQL: ' . mysqli_error($conexion) . '. Revisa que los nombres de tabla/columna (usuarios.correo, espacios_academicos.id/codigo, estudiantes.correo, reservaciones_estudiantes.*) coincidan con tu base de datos real.');
}

$usuarioCache = [];
$espacioCache = [];
$estudianteCache = []; // NUEVO

$insertadas = 0;
$errores = [];
$conflictos = [];

try {
for ($i = 1; $i < count($filas); $i++) {
    $fila = $filas[$i];
    $numFila = $i + 1; // número de fila real en el Excel (1 = encabezado)

    $correo = trim((string)($fila[$colIndex['correo_usuario']] ?? ''));
    $idEspacioRaw = trim((string)($fila[$colIndex['id_espacio']] ?? ''));
    $diaTexto = strtolower(trim((string)($fila[$colIndex['dia_semana']] ?? '')));
    $horaInicio = trim((string)($fila[$colIndex['hora_inicio']] ?? ''));
    $horaFin = trim((string)($fila[$colIndex['hora_fin']] ?? ''));
    $fechaInicioRango = trim((string)($fila[$colIndex['fecha_inicio_rango']] ?? ''));
    $fechaFinRango = trim((string)($fila[$colIndex['fecha_fin_rango']] ?? ''));
    $tipoReservacion = trim((string)($fila[$colIndex['tipo_reservacion']] ?? ''));
    $descripcion = trim((string)($fila[$colIndex['descripcion']] ?? ''));
    $correosEstudiantesRaw = trim((string)($fila[$colIndex['correos_estudiantes']] ?? '')); // NUEVO

    if ($correo === '' && $idEspacioRaw === '') {
        continue; // fila vacía, se ignora silenciosamente
    }

    if ($idEspacioRaw === '' || !ctype_digit($idEspacioRaw)) {
        $errores[] = ['fila' => $numFila, 'detalle' => "id_espacio inválido: '$idEspacioRaw' (debe ser un número entero)"];
        continue;
    }
    $idEspacioRaw = (int)$idEspacioRaw;

    // Validaciones básicas
    if (!isset($diasSemana[$diaTexto])) {
        $errores[] = ['fila' => $numFila, 'detalle' => "Día de la semana inválido: '$diaTexto'"];
        continue;
    }
    $horaInicioDT = DateTime::createFromFormat('H:i', $horaInicio);
    $horaFinDT = DateTime::createFromFormat('H:i', $horaFin);
    if (!$horaInicioDT || !$horaFinDT) {
        $errores[] = ['fila' => $numFila, 'detalle' => 'Formato de hora inválido (usa HH:MM).'];
        continue;
    }
    if ($horaFinDT <= $horaInicioDT) {
        $errores[] = ['fila' => $numFila, 'detalle' => 'La hora de fin debe ser posterior a la hora de inicio.'];
        continue;
    }

    try {
        $rangoInicio = new DateTime($fechaInicioRango);
        $rangoFin = new DateTime($fechaFinRango);
    } catch (Exception $e) {
        $errores[] = ['fila' => $numFila, 'detalle' => 'Formato de fecha inválido (usa AAAA-MM-DD).'];
        continue;
    }
    if ($rangoFin < $rangoInicio) {
        $errores[] = ['fila' => $numFila, 'detalle' => 'fecha_fin_rango es anterior a fecha_inicio_rango.'];
        continue;
    }

    // --- NUEVO: resolver los estudiantes de la fila ANTES de crear las reservas ---
    if ($correosEstudiantesRaw === '') {
        $errores[] = ['fila' => $numFila, 'detalle' => 'No se indicó ningún estudiante en correos_estudiantes.'];
        continue;
    }
    $correosEstudiantes = array_filter(array_map('trim', explode(',', str_replace(';', ',', $correosEstudiantesRaw))));

    $idsEstudiantes = [];
    $estudiantesNoEncontrados = [];
    foreach ($correosEstudiantes as $correoEst) {
        if (!array_key_exists($correoEst, $estudianteCache)) {
            mysqli_stmt_bind_param($stmtEstudiante, 's', $correoEst);
            mysqli_stmt_execute($stmtEstudiante);
            $resEst = mysqli_stmt_get_result($stmtEstudiante);
            $estudianteCache[$correoEst] = $resEst && mysqli_num_rows($resEst) > 0 ? mysqli_fetch_assoc($resEst)['id'] : null;
        }
        if ($estudianteCache[$correoEst] === null) {
            $estudiantesNoEncontrados[] = $correoEst;
        } else {
            $idsEstudiantes[] = $estudianteCache[$correoEst];
        }
    }
    if (!empty($estudiantesNoEncontrados)) {
        $errores[] = ['fila' => $numFila, 'detalle' => 'Estudiante(s) no encontrado(s): ' . implode(', ', $estudiantesNoEncontrados)];
        continue;
    }
    if (empty($idsEstudiantes)) {
        $errores[] = ['fila' => $numFila, 'detalle' => 'No se pudo resolver ningún estudiante válido para esta fila.'];
        continue;
    }
    // --- fin bloque NUEVO ---

    // Usuario (con caché para no repetir consultas)
    if (!array_key_exists($correo, $usuarioCache)) {
        mysqli_stmt_bind_param($stmtUsuario, 's', $correo);
        mysqli_stmt_execute($stmtUsuario);
        $res = mysqli_stmt_get_result($stmtUsuario);
        $usuarioCache[$correo] = $res && mysqli_num_rows($res) > 0 ? mysqli_fetch_assoc($res)['id'] : null;
    }
    $idUsuario = $usuarioCache[$correo];
    if ($idUsuario === null) {
        $errores[] = ['fila' => $numFila, 'detalle' => "Usuario no encontrado: $correo"];
        continue;
    }

    // Espacio (con caché) - se valida que el id_espacio exista realmente en espacios_academicos
    if (!array_key_exists($idEspacioRaw, $espacioCache)) {
        mysqli_stmt_bind_param($stmtEspacio, 'i', $idEspacioRaw);
        mysqli_stmt_execute($stmtEspacio);
        $res = mysqli_stmt_get_result($stmtEspacio);
        $espacioCache[$idEspacioRaw] = $res && mysqli_num_rows($res) > 0 ? mysqli_fetch_assoc($res) : null;
    }
    $espacioInfo = $espacioCache[$idEspacioRaw];
    if ($espacioInfo === null) {
        $errores[] = ['fila' => $numFila, 'detalle' => "id_espacio no encontrado en espacios_academicos: $idEspacioRaw"];
        continue;
    }
    $idEspacio = (int)$espacioInfo['id'];
    $codigoEspacio = $espacioInfo['codigo']; // solo para mostrar en mensajes de conflicto

    // Encontrar la primera fecha dentro del rango que caiga en el día de la semana pedido
    $diaObjetivo = $diasSemana[$diaTexto];
    $fechaCursor = clone $rangoInicio;
    while ((int)$fechaCursor->format('w') !== $diaObjetivo && $fechaCursor <= $rangoFin) {
        $fechaCursor->modify('+1 day');
    }

    // Generar una reservación por cada semana dentro del rango
    while ($fechaCursor <= $rangoFin) {
        $fechaInicioReserva = $fechaCursor->format('Y-m-d') . ' ' . $horaInicioDT->format('H:i:s');
        $fechaFinReserva = $fechaCursor->format('Y-m-d') . ' ' . $horaFinDT->format('H:i:s');

        // Validar conflicto de horario para este espacio
        mysqli_stmt_bind_param(
            $stmtConflicto, 'issssssss',
            $idEspacio,
            $fechaInicioReserva, $fechaFinReserva,
            $fechaInicioReserva, $fechaFinReserva,
            $fechaInicioReserva, $fechaFinReserva,
            $fechaInicioReserva, $fechaFinReserva
        );
        mysqli_stmt_execute($stmtConflicto);
        $resConflicto = mysqli_stmt_get_result($stmtConflicto);
        $dataConflicto = mysqli_fetch_assoc($resConflicto);

        if ($dataConflicto && $dataConflicto['conflictos'] > 0) {
            $conflictos[] = [
                'fila' => $numFila,
                'detalle' => "Conflicto en $codigoEspacio el " . $fechaCursor->format('Y-m-d') . " ($horaInicio-$horaFin)"
            ];
        } else {
            mysqli_stmt_bind_param(
                $stmtInsertar, 'iisssss',
                $idUsuario, $idEspacio, $fechaInicioReserva, $fechaFinReserva,
                $tipoReservacion, $descripcion, $estadoReserva
            );
            if (mysqli_stmt_execute($stmtInsertar)) {
                $insertadas++;
                $idReservacion = mysqli_insert_id($conexion); // NUEVO

                // NUEVO: insertar la relación con cada estudiante de la fila
                foreach ($idsEstudiantes as $idEstudiante) {
                    mysqli_stmt_bind_param($stmtInsertarEstudiante, 'ii', $idReservacion, $idEstudiante);
                    if (!mysqli_stmt_execute($stmtInsertarEstudiante)) {
                        $errores[] = [
                            'fila' => $numFila,
                            'detalle' => "Reserva #$idReservacion creada, pero no se pudo vincular al estudiante id $idEstudiante: " . mysqli_error($conexion)
                        ];
                    }
                }
            } else {
                $errores[] = [
                    'fila' => $numFila,
                    'detalle' => "Error al insertar la reserva del " . $fechaCursor->format('Y-m-d') . ': ' . mysqli_error($conexion)
                ];
            }
        }

        $fechaCursor->modify('+1 week');
    }
}
} catch (Throwable $e) {
    responderError('Error inesperado procesando el archivo (fila aprox. ' . ($numFila ?? '?') . '): ' . $e->getMessage());
}

if (ob_get_length()) { ob_clean(); }
echo json_encode([
    'success' => true,
    'insertadas' => $insertadas,
    'conflictos' => $conflictos,
    'errores' => $errores,
]);