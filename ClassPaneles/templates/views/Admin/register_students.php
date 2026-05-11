<?php
require_once '../../php/conexion_be.php';
include '../../php/admin_session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre_completo']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $identificacion = mysqli_real_escape_string($conexion, $_POST['identificacion']);

    // 🔥 IMPORTANTE: cursos seleccionados
    $cursos = $_POST['cursos'] ?? [];

    // =========================
    // VALIDACIONES
    // =========================
    $verificar_correo = mysqli_query($conexion, "SELECT id FROM estudiantes WHERE correo='$correo'");
    if (mysqli_num_rows($verificar_correo) > 0) {
        echo "<script>alert('Correo ya registrado'); window.location='vista_students.php';</script>";
        exit();
    }

    $verificar_id = mysqli_query($conexion, "SELECT id FROM estudiantes WHERE identificacion='$identificacion'");
    if (mysqli_num_rows($verificar_id) > 0) {
        echo "<script>alert('Identificación ya registrada'); window.location='vista_students.php';</script>";
        exit();
    }

    // =========================
    // IMAGEN
    // =========================
    $imagen = null;

    if (!empty($_FILES['imagen']['name'])) {

        $directorio = "../../uploads/estudiantes/";

        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombre_img = uniqid() . "_" . basename($_FILES['imagen']['name']);
        $ruta = $directorio . $nombre_img;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            $imagen = $ruta;
        }
    }

    // =========================
    // INSERT ESTUDIANTE (SOLO UNA VEZ)
    // =========================
    $query = "INSERT INTO estudiantes (nombre_completo, correo, identificacion, imagen)
              VALUES ('$nombre_completo', '$correo', '$identificacion', '$imagen')";

    if (mysqli_query($conexion, $query)) {

        // 🔥 ID del estudiante creado
        $estudiante_id = mysqli_insert_id($conexion);

        // =========================
        // INSERT CURSOS
        // =========================
        if (!empty($cursos)) {
            foreach ($cursos as $curso_id) {

                $curso_id = (int)$curso_id;

                $queryRel = "INSERT INTO cursos_estudiantes (estudiante_id, curso_id)
                            VALUES ($estudiante_id, $curso_id)";

                mysqli_query($conexion, $queryRel);
            }
        }

        echo "<script>
            alert('Estudiante registrado correctamente');
            window.location='vista_students.php';
        </script>";

    } else {
        echo "<script>
            alert('Error al registrar estudiante');
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Estudiante</title>
    <link rel="stylesheet" href="../../assets/css/style_paneles.css">
</head>

<body>
    <main>
        <div class="profile-container">
            <img src="<?php echo $imagen; ?>" alt="Foto de perfil" class="profile-img">
            <h3 class="profile-name_user"><?php echo htmlspecialchars($nombre_completo); ?></h3>
            <h3 class="profile-name"><?php echo htmlspecialchars($rol); ?></h3>
            <a href="../../php/cerrar_sesion.php" class="logout">
                <img src="../../assets/images/cerrar-sesion.png" alt="Cerrar sesión" class="icons-image">
            </a>
            <a href="../../php/config.php" class="config">
                <img src="../../assets/images/config.png" alt="Configuracion" class="icons-image">
            </a>
            <a href="admin_dashboard.php" class="home-admin">
                <img src="../../assets/images/inicio.png" alt="inicio" class="icons-image">
            </a>

            <div class="menu-container" id="menu-container">
                <div class="menu-link" onclick="toggleDropdown()">Cuenta<span>▼</span>
                </div>
                <div class="submenu" id="submenu">
                    <a href="create_account.php">Crear Cuenta</a>
                    <a href="vista_cuentas.php">cuentas </a>
                    <a href="register_students.php">Añadir Estudiantes</a>
                    <a href="vista_students.php">Estudiantes</a>
                </div>
            </div>
            <div class="menu-container_espacios" id="menu-container_espacios">
                <div class="menu-link" onclick="toggleDropdown_space()">Espacios<span>▼</span>
                </div>
                <div class="submenu" id="submenu_espacios">
                    <a href="register_buldings.php">Añadir Edificios</a>
                    <a href="table_build.php">Edificios</a>
                    <a href="equipment.php">Equipamientos</a>
                    <a href="vista_students.php">Salones</a>
                </div>
            </div>
        </div>
        <h1 class="title-register_students">Registrar Estudiante</h1>
        <div class="container-form_register_students">
            <form method="POST" enctype="multipart/form-data" class="formulario_register">
                <input type="text" placeholder="Nombres y apellidos completos" id="nombre_completo" name="nombre_completo" required>

                <input type="email" placeholder="ej: ejemplo@gmail.com" id="correo" name="correo" required>

                <input type="text" placeholder="Cedula o documento de identidad" id="identificacion" name="identificacion" required>

                <input type="file" id="imagen" name="imagen" accept="image/*">

                <button type="submit">Registrar Estudiante</button>
            </form>
        </div>
    </main>
    <script src="../../assets/js/script_stats.js"></script>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/script_menu.js"></script>
</body>

</html>