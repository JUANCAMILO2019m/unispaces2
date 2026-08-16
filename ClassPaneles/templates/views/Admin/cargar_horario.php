<?php
include '../../php/admin_session.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../templates/index.php");
    exit();
}
include '../../php/conexion_be.php';

$currentFile = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Horario de Cursos</title>
    <link rel="stylesheet" href="../../assets/css/style_panel.css?v=1">
    <link rel="shortcut icon" href="../../assets/images/logo2.png">
</head>
<body>

<style>
    .upload-box {
        max-width: 720px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 24px 28px;
        margin-top: 16px;
    }
    .upload-box h2 { margin-top: 0; }
    .upload-box p.hint { color: #666; font-size: 14px; }
    .upload-box input[type="file"] { margin: 12px 0; }
    .upload-actions { display: flex; gap: 12px; align-items: center; margin-top: 12px; }
    .btn-primary {
        background: #2f6fed; color: #fff; border: none; padding: 10px 18px;
        border-radius: 6px; cursor: pointer; font-size: 14px;
    }
    .btn-primary:disabled { opacity: .6; cursor: not-allowed; }
    .btn-secondary {
        background: #f1f1f1; color: #333; border: 1px solid #ddd; padding: 10px 18px;
        border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none;
    }
    .checkbox-row { margin: 10px 0; font-size: 14px; }
    #resultado { margin-top: 18px; }
    .result-summary { padding: 12px 14px; border-radius: 8px; margin-bottom: 10px; font-size: 14px; }
    .result-ok { background: #dbf8d7; color: #1e6b17; }
    .result-warn { background: #fff3cd; color: #7a5c00; }
    .result-error { background: #f8d7da; color: #721c24; }
    table.detalle { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
    table.detalle th, table.detalle td { border: 1px solid #eee; padding: 6px 8px; text-align: left; }
    table.detalle th { background: #fafafa; }
    .fila-error { background: #fdecea; }
    .fila-conflicto { background: #fff8e1; }
</style>

<div class="container">
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
                    <li><a href="cargar_horario.php"
                            class="<?php echo $currentFile == 'cargar_horario.php' ? 'active' : ''; ?>">
                            <ion-icon name="cloud-upload-outline"></ion-icon> Cargar Horario
                        </a></li>
                    <li><a href="asistencias_docente.php"
                            class="<?php echo $currentFile == 'asistencias_docente.php' ? 'active' : ''; ?>">
                            <ion-icon name="calendar-outline"></ion-icon> Asistencias
                        </a></li>
                    <li><a href="table_equipment_reports.php"
                            class="<?php echo $currentFile == 'table_equipment_reports.php' ? 'active' : ''; ?>">
                            <ion-icon name="calendar-outline"></ion-icon> Reportes equipamientos
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

    <main class="main-content-cuenta">
        <h1 class="title-table">Cargar Horario de Cursos</h1>

        <div class="upload-box">
            <h2>Subir archivo Excel (.xlsx)</h2>
            <p class="hint">
                Cada fila del archivo representa un curso con un horario semanal recurrente
                (por ejemplo, "todos los lunes de 7:00 a 9:00 entre el 19 de enero y el 30 de mayo").
                El sistema creará automáticamente una reservación por cada semana dentro de ese rango.
            </p>
            <p class="hint">
                Columnas requeridas: <code>correo_usuario</code>, <code>codigo_espacio</code>,
                <code>dia_semana</code>, <code>hora_inicio</code>, <code>hora_fin</code>,
                <code>fecha_inicio_rango</code>, <code>fecha_fin_rango</code>,
                <code>tipo_reservacion</code>, <code>descripcion</code>.
            </p>
            <a href="../../assets/plantillas/plantilla_horarios.xlsx" class="btn-secondary" download>
                Descargar plantilla de ejemplo
            </a>

            <form id="formHorario" enctype="multipart/form-data">
                <input type="file" name="archivo_horario" id="archivo_horario" accept=".xlsx" required>

                <div class="checkbox-row">
                    <label>
                        <input type="checkbox" name="aprobar_automatico" id="aprobar_automatico">
                        Aprobar automáticamente las reservaciones generadas (si no se marca, quedan
                        como "pendiente" para revisión).
                    </label>
                </div>

                <div class="upload-actions">
                    <button type="submit" class="btn-primary" id="btnSubir">Procesar y cargar</button>
                    <a href="table_reservation.php" class="btn-secondary">Ver reservas</a>
                </div>
            </form>

            <div id="resultado"></div>
        </div>
    </main>
</div>

<script>
document.getElementById('formHorario').addEventListener('submit', function (e) {
    e.preventDefault();

    const btn = document.getElementById('btnSubir');
    const resultadoDiv = document.getElementById('resultado');
    const fileInput = document.getElementById('archivo_horario');

    if (!fileInput.files.length) {
        alert('Selecciona un archivo .xlsx primero.');
        return;
    }

    const formData = new FormData();
    formData.append('archivo_horario', fileInput.files[0]);
    formData.append('aprobar_automatico', document.getElementById('aprobar_automatico').checked ? 'aceptada' : 'pendiente');

    btn.disabled = true;
    btn.textContent = 'Procesando...';
    resultadoDiv.innerHTML = '';

    fetch('procesar_horario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Procesar y cargar';

        if (!data.success) {
            resultadoDiv.innerHTML = `<div class="result-summary result-error">Error: ${data.error}</div>`;
            return;
        }

        let html = `<div class="result-summary result-ok">
            ${data.insertadas} reservación(es) creada(s) correctamente.
        </div>`;

        if (data.conflictos && data.conflictos.length > 0) {
            html += `<div class="result-summary result-warn">
                ${data.conflictos.length} fila(s) con conflicto de horario (no se insertaron).
            </div>`;
        }
        if (data.errores && data.errores.length > 0) {
            html += `<div class="result-summary result-error">
                ${data.errores.length} fila(s) con errores (no se insertaron).
            </div>`;
        }

        if ((data.conflictos && data.conflictos.length) || (data.errores && data.errores.length)) {
            html += '<table class="detalle"><thead><tr><th>Fila</th><th>Motivo</th><th>Detalle</th></tr></thead><tbody>';
            (data.conflictos || []).forEach(c => {
                html += `<tr class="fila-conflicto"><td>${c.fila}</td><td>Conflicto de horario</td><td>${c.detalle}</td></tr>`;
            });
            (data.errores || []).forEach(err => {
                html += `<tr class="fila-error"><td>${err.fila}</td><td>Error</td><td>${err.detalle}</td></tr>`;
            });
            html += '</tbody></table>';
        }

        resultadoDiv.innerHTML = html;

        if (data.insertadas > 0) {
            setTimeout(() => { window.location.href = 'table_reservation.php'; }, 2500);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Procesar y cargar';
        resultadoDiv.innerHTML = `<div class="result-summary result-error">
            Hubo un problema con la solicitud: ${err}
        </div>`;
    });
});
</script>

<script src="../../assets/js/script_menu.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</body>
</html>