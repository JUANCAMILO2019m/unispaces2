<?php
include '../../php/admin_session.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../templates/index.php");
    exit();
}

include '../../php/conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener y limpiar nombre del curso
    $nombre_curso = trim(mysqli_real_escape_string($conexion, $_POST['nombre_curso']));

    // Validar campo vacío
    if (empty($nombre_curso)) {
        echo "
        <script>
            alert('El nombre del curso no puede estar vacío.');
            window.location = 'vista_students.php';
        </script>";
        exit();
    }

    // Verificar si ya existe
    $verificar = "SELECT id FROM cursos WHERE nombre_curso = '$nombre_curso'";
    $resultado_verificar = mysqli_query($conexion, $verificar);

    if (mysqli_num_rows($resultado_verificar) > 0) {
        echo "
        <script>
            alert('El curso ya existe.');
            window.location = 'vista_students.php';
        </script>";
        exit();
    }

    // Insertar curso
    $query_insert = "INSERT INTO cursos (nombre_curso) VALUES ('$nombre_curso')";

    if (mysqli_query($conexion, $query_insert)) {
        echo "
        <script>
            alert('Curso creado correctamente.');
            window.location = 'vista_students.php';
        </script>";
    } else {
        echo "
        <script>
            alert('Error al crear el curso.');
            window.location = 'vista_students.php';
        </script>";
    }

    mysqli_close($conexion);
}
?>