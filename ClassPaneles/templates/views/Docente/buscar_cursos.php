<?php
include '../../php/conexion_be.php';

$query = $_GET['query'];

$sql = "SELECT id, nombre_curso 
        FROM cursos 
        WHERE nombre_curso LIKE '%$query%' 
        LIMIT 10";

$result = mysqli_query($conexion, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>