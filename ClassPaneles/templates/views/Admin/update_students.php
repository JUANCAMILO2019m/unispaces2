<?php
require_once '../../php/conexion_be.php';
include '../../php/admin_session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vista_students.php');
    exit;
}

if (!isset($_POST['id']) || empty($_POST['id'])) {
    header('Location: vista_students.php');
    exit;
}

$id = mysqli_real_escape_string($conexion, $_POST['id']);

$id = mysqli_real_escape_string($conexion, $_POST['id']);

$query_usuario = "SELECT id FROM estudiantes WHERE id = '$id'";
$resultado_usuario = mysqli_query($conexion, $query_usuario);

if (mysqli_num_rows($resultado_usuario) === 0) {
    header('Location: vista_cuentas.php');
    exit;
}

include '../../php/update_table_students.php';

/* ===============================
ACTUALIZAR CURSOS DEL ESTUDIANTE
=============================== */

$cursosSeleccionados = json_decode($_POST['cursos_seleccionados'], true);

// Eliminar cursos anteriores
mysqli_query($conexion, "DELETE FROM cursos_estudiantes WHERE estudiante_id = $id");

// Insertar nuevos cursos
if (!empty($cursosSeleccionados)) {
    foreach ($cursosSeleccionados as $cursoId) {
        $cursoId = (int)$cursoId;

        mysqli_query(
            $conexion,
            "INSERT INTO cursos_estudiantes (estudiante_id, curso_id)
            VALUES ($id, $cursoId)"
        );
    }
}

header("Location: vista_students.php?update=success");
exit();