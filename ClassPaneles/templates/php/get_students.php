<?php
include 'admin_session.php';
include 'conexion_be.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID no recibido']);
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del estudiante
$query = "
    SELECT id, nombre_completo, correo, identificacion, imagen
    FROM estudiantes
    WHERE id = $id
";

$result = mysqli_query($conexion, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Solo leer una vez
$student = mysqli_fetch_assoc($result);

// Obtener cursos asociados
$queryCursos = "
    SELECT c.id, c.nombre_curso
    FROM cursos c
    INNER JOIN cursos_estudiantes ce ON c.id = ce.curso_id
    WHERE ce.estudiante_id = $id
    ORDER BY c.nombre_curso ASC
";

$resultCursos = mysqli_query($conexion, $queryCursos);

$cursos = [];
if ($resultCursos) {
    while ($row = mysqli_fetch_assoc($resultCursos)) {
        $cursos[] = $row;
    }
}

$student['cursos'] = $cursos;

// Devolver JSON completo con estudiante + cursos
echo json_encode($student);

mysqli_close($conexion);
?>
