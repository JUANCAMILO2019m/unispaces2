<?php
require_once '../../php/conexion_be.php';
include '../../php/admin_session.php';

// Verificar si se recibió un ID válido
if (!isset($_GET['id'])) {
    echo "<script>alert('No se especificó un espacio válido.'); window.location.href='register_buldings.php';</script>";
    exit;
}

// Obtener y sanitizar el ID recibido
$equip_id = mysqli_real_escape_string($conexion, $_GET['id']);

// Consultar datos del equipamiento
$query_equip = "SELECT * FROM equipamiento WHERE id = '$equip_id'";
$resultado_equipamiento = mysqli_query($conexion, $query_equip);

if (mysqli_num_rows($resultado_equipamiento) > 0) {
    $id = mysqli_fetch_assoc($resultado_equipamiento);  // Aquí se almacena el array de datos del espacio  // Asignamos el ID del edificio del espacio
} else {
    echo "<script>alert('Espacio de edificio no encontrado.'); window.location.href='register_buldings.php';</script>";
    exit;
}

// Procesar formulario de actualización de descripción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se envió el formulario de descripción
    if (isset($_POST['update_description_space'])) {
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

        // Actualiza solo la descripción
        $query_update = "UPDATE equipamiento SET descripcion='$descripcion' WHERE id='$equip_id'";
        if (mysqli_query($conexion, $query_update)) {
            echo "<script>alert('Descripción actualizada con éxito.'); window.location.href='equipment.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar la descripción: " . mysqli_error($conexion) . "');</script>";
        }
    }

    // Si se envió el formulario de actualización del espacio
    if (isset($_POST['update_equip'])) {
        $codigo = mysqli_real_escape_string($conexion, $_POST['codigo']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $estado = mysqli_real_escape_string($conexion, $_POST['estado']);
        // Condición para la descripción
        $descripcion = isset($_POST['descripcion']) ? mysqli_real_escape_string($conexion, $_POST['descripcion']) : (isset($id['descripcion']) ? $id['descripcion'] : '');

        // Imagen
        $imagen = $id['imagen'];

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombre_imagen = $_FILES['imagen']['name'];
            $ruta_temp = $_FILES['imagen']['tmp_name'];
            $directorio_destino = "../../uploads/espacio/";

            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }

            $imagen = $directorio_destino . uniqid() . "_" . basename($nombre_imagen);

            if (!move_uploaded_file($ruta_temp, $imagen)) {
                echo "<script>alert('Error al subir la imagen.');</script>";
                $imagen = $id['imagen'];
            }
        }

        // Actualización de espacio
        $query_update = "UPDATE equipamiento SET
            codigo='$codigo',
            nombre='$nombre',
            descripcion='$descripcion',
            imagen='$imagen',
            estado='$estado'
            WHERE id='$equip_id'";
        if (mysqli_query($conexion, $query_update)) {
            echo "<script>alert('Equipamiento actualizado con éxito.'); window.location.href='equipment.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el Equipamiento: " . mysqli_error($conexion) . "');</script>";
        }
    }
}

// Consultar espacios
$query = "SELECT id, codigo, imagen, edificio_id FROM espacios_academicos";
$result = mysqli_query($conexion, $query);
$espacios = [];

while ($row = mysqli_fetch_assoc($result)) {
    $espacios[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Equipamiento</title>
    <link rel="stylesheet" href="../../assets/css/style_panel.css">
    <link rel="stylesheet" href="../../assets/css/update_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css">
</head>
</head>
<body>
<div class="container-docente">
<aside class="sidebar">
<div class="logo">
                    <img src="../../assets/images/logo2.png" alt="Logo" class="logo-img" width="150" height="auto">
                </div>
                <nav class="menu">
                    <div class="menu-group">
                        <p class="menu-title">Menú Principal</p>
                        <ul>
                            <li><a href="admin_dashboard.php"
                                    class="<?php echo $currentFile == 'admin_dashboard.php' ? 'active' : ''; ?>">
                                    <ion-icon name="home-outline"></ion-icon> Inicio
                                </a></li>
                            <li><a href="vista_cuentas.php"
                                    class="<?php echo $currentFile == 'vista_cuentas.php' ? 'active' : ''; ?>">
                                    <ion-icon name="people-outline"></ion-icon> Cuentas
                                </a></li>
                            <li><a href="vista_students.php"
                                    class="<?php echo $currentFile == 'vista_students.php' ? 'active' : ''; ?>">
                                    <ion-icon name="person-outline"></ion-icon> Estudiantes
                                </a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <p class="menu-title">Gestión de Espacios</p>
                        <ul>
                            <li><a href="./register_buldings.php"
                                    class="<?php echo $currentFile == 'register_buildings.php' ? 'active' : ''; ?>">
                                    <ion-icon name="home-outline"></ion-icon> Añadir Edificios
                                </a></li>
                            <li><a href="table_build.php"
                                    class="<?php echo $currentFile == 'table_build.php' ? 'active' : ''; ?>">
                                    <ion-icon name="list-outline"></ion-icon> Edificios
                                </a></li>
                            <li><a href="equipment.php"
                                    class="<?php echo $currentFile == 'equipment.php' ? 'active' : ''; ?>">
                                    <ion-icon name="construct-outline"></ion-icon> Equipamientos
                                </a></li>
                            <li><a href="table_reservation.php"
                                    class="<?php echo $currentFile == 'table_reservation.php' ? 'active' : ''; ?>">
                                    <ion-icon name="calendar-outline"></ion-icon> Reservas
                                </a></li>
                            <li><a href="table_equipment_reports.php"
                                    class="<?php echo $currentFile == 'table_equipment_reports.php' ? 'active' : ''; ?>">
                                    <ion-icon name="calendar-outline"></ion-icon> Reportes equipamientos
                                </a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <p class="menu-title">Mensajeria</p>
                        <ul>
                            <li><a href="messages.php"
                                    class="<?php echo $currentFile == 'messages.php' ? 'active' : ''; ?>">
                                    <ion-icon name="calendar-outline"></ion-icon> Buzon ayuda
                                </a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <p class="menu-title">Configuración</p>
                        <ul>
                            <li><a href="../../php/config.php"
                                    class="<?php echo $currentFile == 'config.php' ? 'active' : ''; ?>">
                                    <ion-icon name="settings-outline"></ion-icon> Ajustes
                                </a></li>
                            <li><a href="../../php/cerrar_sesion_admin.php"
                                    class="<?php echo $currentFile == 'cerrar_sesion_admin.php' ? 'active' : ''; ?>">
                                    <ion-icon name="log-out-outline"></ion-icon> Cerrar Sesión
                                </a></li>
                        </ul>
                    </div>
                </nav>
                <div class="divider"></div>
                <div class="profile">
                    <img src="<?php echo $imagen; ?>" alt="Foto de perfil" class="profile-img">
                    <div>
                        <p class="user-name"><?php echo htmlspecialchars($nombre_completo); ?></p>
                        <p class="user-email"> <?php echo htmlspecialchars($correo); ?></p>
                    </div>
                </div>
    </aside>
<div id="main-content">
<div class="header-container">

<div class="back-button-container">
<button class="back-button" onclick="window.history.back()">
<i class="fa-solid fa-arrow-left"></i> Volver
</button>
</div>

<div class="edit-button-container">
<button id="edit-mode-btn" class="edit-button">
<i class="fa-solid fa-pen"></i> Modo Edición
</button>

<div class="action-buttons">
<button type="button" id="cancel-edit-btn" class="cancel-btn">
<i class="fas fa-times"></i> Cancelar
</button>

<button type="submit" form="edit-form" class="save-btn">
<i class="fa-solid fa-floppy-disk"></i> Guardar
</button>
</div>
</div>
</div>

<form id="edit-form" method="POST" enctype="multipart/form-data">

<input type="hidden" name="update_equip" value="1">
<input type="hidden" name="id" value="<?php echo $id['id']; ?>">

<div class="rectangle">
    <div class="half">
        <div class="image-container space-image">

            <img src="<?php echo htmlspecialchars($id['imagen']); ?>"
                class="building-image">

            <!-- CAMBIAR IMAGEN (SOLO EDICIÓN) -->
            <label for="imagen" class="change-image-btn">
                    <i class="fa-solid fa-camera camera-icon"></i>
                    <div class="change-image-text">Cambiar imagen</div>
                    <div class="subtext">Haz clic para seleccionar</div>
            </label>
            <input type="file" id="imagen" name="imagen" class="file-input" accept="image/*">

        </div>
    </div>

    <div class="half">
            <div class="tabs">
            <button type="button" class="tab-button active"
            data-tab="info">
            <i class="fa-solid fa-circle-info"></i> Información
            </button>
    </div>
    <div class="tab-content" id="info">

<!-- DESCRIPCIÓN -->
<div class="detail-item-descript-spaces">
    <div class="details-grid-descrip">
        <div class="collapsible-content">

            <!-- modo vista -->
            <p class="hidden-in-edit-mode">
                <?php echo htmlspecialchars($id['descripcion']); ?>
            </p>

            <!-- modo edición -->
            <textarea name="descripcion"
            class="editable-textarea hidden-in-view-mode">
            <?php echo htmlspecialchars($id['descripcion']); ?>
            </textarea>

        </div>
    </div>
</div>

<!-- GRID DE INFORMACIÓN -->
<div class="details-grid">

    <!-- ESTADO -->
    <div class="detail-item">
        <i class="ti ti-users"></i>
        <div>

            <span class="detail-label">Estado</span>

            <span class="detail-value hidden-in-edit-mode">
                <?php echo htmlspecialchars($id['estado']); ?>
            </span>

            <select name="estado" class="editable-field hidden-in-view-mode">
                <option value="Disponible" <?php echo ($id['estado'] === 'Disponible') ? 'selected' : ''; ?>>Disponible</option>
                <option value="En Mantenimiento" <?php echo ($id['estado'] === 'En Mantenimiento') ? 'selected' : ''; ?>>En Mantenimiento</option>
                <option value="No Disponible" <?php echo ($id['estado'] === 'No Disponible') ? 'selected' : ''; ?>>No Disponible</option>
            </select>

        </div>
    </div>

    <!-- NOMBRE -->
    <div class="detail-item">
        <i class="ti ti-building"></i>
        <div>

            <span class="detail-label">Nombre</span>

            <span class="detail-value hidden-in-edit-mode">
                <?php echo htmlspecialchars($id['nombre']); ?>
            </span>

            <input type="text"
            name="nombre"
            value="<?php echo htmlspecialchars($id['nombre']); ?>"
            class="editable-field hidden-in-view-mode">

        </div>
    </div>

    <!-- CÓDIGO -->
    <div class="detail-item">
        <i class="ti ti-hash"></i>
        <div>

            <span class="detail-label">Código</span>

            <span class="detail-value hidden-in-edit-mode">
                <?php echo htmlspecialchars($id['codigo']); ?>
            </span>

            <input type="text"
            name="codigo"
            value="<?php echo htmlspecialchars($id['codigo']); ?>"
            class="editable-field hidden-in-view-mode">

        </div>
    </div>

</div>
</div>

</div>
<script src="../../assets/js/building_edit.js"></script>

<script>
document.querySelectorAll('.tab-button').forEach(btn=>{
btn.addEventListener('click',()=>{
document.querySelectorAll('.tab-button')
.forEach(b=>b.classList.remove('active'));

document.querySelectorAll('.tab-content')
.forEach(c=>c.style.display='none');

btn.classList.add('active');
document.getElementById(btn.dataset.tab).style.display='block';
});
});
</script>
<script>
        function openModal() {
                document.getElementById("modal").style.display = "block";
            }
        function cerrarModal(){
            document.getElementById("modal").style.display = "none";
        }
            // Cerrar el modal cuando se haga clic fuera del modal
        window.onclick = function(event) {
                if (event.target === document.getElementById("modal")) {
                    document.getElementById("modal").style.display = "none";
                }
        }
        function abrirModalReporte(equipamiento_id, espacioId) {
            console.log("Abriendo modal para ID:", equipamiento_id); 
            document.getElementById("espacio_equipamiento_id").value = equipamiento_id;
            document.getElementById("espacio_id").value = espacioId;
            document.getElementById("modalReporteEquipamiento").style.display = "block";
            document.getElementById("modal-reject").style.display = "block";
        }

        function cerrarModalReporte() {
            document.getElementById("modalReporteEquipamiento").style.display = "none";
        }

        // Cerrar el modal cuando se haga clic fuera del contenido
        window.onclick = function(event) {
            let modal = document.getElementById("modalReporteEquipamiento");
            if (event.target === modal) {
                cerrarModalReporte();
            }
        }
    </script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</body>

</html>