<?php
include '../../php/conexion_be.php';

$id = $_GET['id'];

// Datos básicos del estudiante
$query = "SELECT id, nombre_completo, correo, identificacion, imagen FROM estudiantes WHERE id = $id";
$result = mysqli_query($conexion, $query);
$student = mysqli_fetch_assoc($result);

// Cursos asociados
$queryCursos = "SELECT c.id, c.nombre_curso 
                FROM cursos c
                INNER JOIN cursos_estudiantes ec ON c.id = ec.curso_id
                WHERE ec.estudiante_id = $id";
$resultCursos = mysqli_query($conexion, $queryCursos);
$cursos = [];
while ($row = mysqli_fetch_assoc($resultCursos)) {
    $cursos[] = $row;
}

$student['cursos'] = $cursos;

echo json_encode($student);
?>