<?php
include '../../php/admin_session.php';
 
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../templates/index.php");
    exit();
}
 
include '../../php/conexion_be.php';
 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: vista_students.php");
    exit();
}
 
$id = (int) $_GET['id'];
 
// 1. Eliminar relaciones en cursos_estudiantes
$deleteRelaciones = "DELETE FROM cursos_estudiantes WHERE curso_id = $id";
if (!mysqli_query($conexion, $deleteRelaciones)) {
    die("Error al eliminar relaciones del curso: " . mysqli_error($conexion));
}
 
// 2. Eliminar el curso
$deleteCurso = "DELETE FROM cursos WHERE id = $id";
if (!mysqli_query($conexion, $deleteCurso)) {
    die("Error al eliminar el curso: " . mysqli_error($conexion));
}
 
mysqli_close($conexion);
 
header("Location: vista_students.php?openViewCourses=1");
exit();
?>