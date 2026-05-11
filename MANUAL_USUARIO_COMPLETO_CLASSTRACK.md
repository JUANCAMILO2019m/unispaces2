# Manual de Usuario y Mapa Completo del Proyecto ClassTrack

Fecha de elaboracion: 2026-04-11
Base analizada: codigo fuente actual del proyecto en `C:\xampp\htdocs\ClassTrack`

## 1. Proposito del proyecto

ClassTrack es una plataforma web en PHP orientada a la gestion de espacios academicos universitarios. El sistema permite:

- Administrar edificios, aulas y equipamientos.
- Registrar cuentas de administradores y docentes.
- Registrar estudiantes.
- Solicitar, aprobar, rechazar y dar seguimiento a reservas de espacios.
- Llevar asistencia de estudiantes por reserva.
- Reportar novedades de equipamiento.
- Gestionar mensajes de soporte entre docentes y administradores.
- Exportar informacion y generar reportes en PDF.

En varios archivos todavia aparecen nombres anteriores como `Unispace`, por lo que el sistema mezcla ambos nombres en textos, correos y titulos.

## 2. Tecnologias y dependencias

### Backend

- PHP nativo con `mysqli`.
- Base de datos MySQL/MariaDB: `login_register_db`.
- Manejo de sesiones por rol.
- PHPMailer para correos.
- TCPDF para reportes PDF.
- vlucas/phpdotenv para variables de entorno.

### Frontend

- HTML, CSS y JavaScript sin framework.
- Ionicons, Font Awesome, Tabler Icons y Chart.js.
- Carpeta adicional `fullcalendar-6.1.15` incluida como dependencia externa, aunque en las vistas inspeccionadas no aparece integrada de forma central.

### Dependencias definidas en Composer

Archivo: `ClassPaneles/templates/composer.json`

- `phpmailer/phpmailer`
- `tecnickcom/tcpdf`
- `vlucas/phpdotenv`

## 3. Estructura general del proyecto

### Raiz del repositorio

- `.env`: variables SMTP del proyecto.
- `consulta obtener datos en docente_session.php`: archivo auxiliar de notas/cambios, no es parte del flujo de la app.
- `fullcalendar-6.1.15/`: libreria externa.
- `ClassPaneles/`: aplicacion principal.
- `MANUAL_USUARIO_CLASSTRACK_SECCIONES.md`: manual previo existente.

### Aplicacion principal

Ruta base: `ClassPaneles/templates`

Contiene:

- `index.php`: acceso principal al sistema.
- `views/Admin/`: vistas del rol administrador.
- `views/Docente/`: vistas del rol docente.
- `php/`: scripts de autenticacion, sesiones, exportacion, correo y actualizacion.
- `assets/css/`: estilos.
- `assets/js/`: scripts de interfaz.
- `assets/images/`: imagenes estaticas del sistema.
- `uploads/`: archivos subidos por usuarios.
- `vendor/`: dependencias Composer.

## 4. Roles del sistema

### Administrador

Puede:

- Ver dashboard general.
- Crear y eliminar cuentas de usuario.
- Registrar estudiantes.
- Registrar edificios.
- Crear espacios por edificio.
- Registrar equipamiento.
- Asignar equipamiento a espacios.
- Aprobar o rechazar reservas.
- Ver asistencias.
- Atender mensajes de soporte.
- Aprobar o rechazar reportes de equipamiento.
- Actualizar sus propios datos.

### Docente

Puede:

- Ver dashboard personal.
- Explorar edificios y espacios.
- Consultar disponibilidad.
- Solicitar reservas.
- Editar sus reservas pendientes.
- Registrar asistencia de estudiantes en una reserva activa.
- Enviar solicitudes de soporte.
- Consultar respuestas a sus solicitudes.
- Reportar el estado de equipamientos.
- Descargar reporte PDF de una reserva finalizada.
- Actualizar su cuenta y activar/desactivar notificaciones por correo.

### Estudiante

No tiene panel independiente en el codigo inspeccionado. El estudiante participa como registro asociado a reservas hechas por docentes. Existe `students_session.php`, pero no hay una vista dedicada de panel estudiantil dentro del proyecto revisado.

## 5. Flujo general de uso

### Acceso

1. El usuario entra por `ClassPaneles/templates/index.php`.
2. Ingresa correo y contrasena.
3. `php/login_be.php` valida credenciales.
4. Segun el rol, redirige a:
   - Administrador: `views/Admin/admin_dashboard.php`
   - Docente: `views/Docente/docente_dashboard.php`

### Flujo tipico del docente

1. Explora edificios o disponibilidad.
2. Abre un espacio concreto.
3. Realiza una solicitud de reserva agregando estudiantes.
4. El estado inicial queda en `Pendiente` o `pendiente` segun la ruta usada.
5. El administrador la aprueba o la rechaza.
6. Si la reserva entra en ventana activa, puede pasar a estado `asistencia`.
7. El docente registra asistencia y la reserva termina en `finalizado`.
8. Luego puede descargar un reporte PDF.

### Flujo tipico del administrador

1. Registra estructura fisica: edificios, espacios, equipamientos.
2. Revisa reservas pendientes.
3. Aprueba o rechaza solicitudes.
4. Atiende mensajes de soporte.
5. Revisa reportes de equipamiento enviados por docentes.
6. Consulta paneles de control y estadisticas.

## 6. Pagina de acceso y recuperacion de cuenta

### `ClassPaneles/templates/index.php`

Funcion:

- Pantalla principal de login.
- Solicita correo y contrasena.
- Tiene icono para mostrar/ocultar contrasena.
- Enlace a recuperacion de contrasena.

Acciones:

- Envia el formulario a `php/login_be.php`.
- Enlace a `php/forgot_password.php`.

### `ClassPaneles/templates/php/forgot_password.php`

Funcion:

- Pantalla para solicitar recuperacion de contrasena por correo.
- Pide el correo del usuario.

Accion:

- Envia el formulario a `send_reset_link.php`.

### `ClassPaneles/templates/php/send_reset_link.php`

Funcion:

- Verifica si el correo existe en tabla `usuarios`.
- Genera token de recuperacion con expiracion de 1 hora.
- Actualiza la tabla `usuarios`.
- Envia correo con PHPMailer usando variables SMTP de `.env`.

### `ClassPaneles/templates/php/reset_password.php`

Funcion:

- Muestra la pagina de restablecimiento si el token es valido y no ha expirado.
- Solicita nueva contrasena y confirmacion.

### `ClassPaneles/templates/php/update_password.php`

Funcion:

- Valida coincidencia de contrasenas.
- Actualiza contrasena con hash `sha512`.
- Limpia token y fecha de expiracion.

## 7. Vistas del administrador

### 7.1 Dashboard y navegacion principal

#### `views/Admin/admin_dashboard.php`

Titulo: `Panel Administrador`

Que muestra:

- Saludo personalizado.
- Total de docentes registrados.
- Total de estudiantes registrados.
- Tendencias de crecimiento o disminucion.
- Ultimos cambios del dia en altas y bajas.
- Resumen grafico de estados de reservas por ano actual y anterior.
- Grafico de reservas por mes.
- Panel lateral con accesos a cuentas, estudiantes, edificios, equipamientos, reservas, asistencias, reportes de equipamiento y buzon de ayuda.
- Lista resumida de reportes recientes de equipamiento.

Datos clave que consulta:

- `usuarios`
- `estudiantes`
- `estadisticas`
- `reservaciones`
- `solicitudes_reporte_docente`
- tablas historicas de eliminacion

Observacion:

- El dashboard tambien inserta registros en `estadisticas` cuando detecta cambios en totales.

### 7.2 Gestion de cuentas

#### `views/Admin/vista_cuentas.php`

Titulo: `Ver Usuarios`

Funcion:

- Lista de usuarios registrados.
- Busqueda por nombre, correo o usuario.
- Creacion de nuevas cuentas desde modal/formulario.
- Edicion de usuario existente.
- Eliminacion de usuarios.

Columnas esperadas:

- Imagen
- Nombre completo
- Correo
- Usuario
- Rol

Acciones visibles:

- Crear nuevo usuario.
- Actualizar usuario.
- Eliminar usuario.

Relacion con backend:

- Alta: `php/registro_usuario_be.php`
- Edicion: `views/Admin/update_user.php`
- Eliminacion: directo en la vista, con respaldo en `usuarios_eliminados`.

#### `views/Admin/create_account.php`

Funcion:

- Pagina/formulario alterno para crear cuentas.
- En la version actual parece una ruta secundaria o heredada frente al modal incluido en `vista_cuentas.php`.

#### `views/Admin/update_user.php`

Funcion:

- Endpoint de actualizacion del usuario seleccionado.
- Recibe datos por POST y ejecuta `UPDATE`.
- Redirige nuevamente a `vista_cuentas.php`.

### 7.3 Gestion de estudiantes

#### `views/Admin/vista_students.php`

Titulo: `Ver Estudiantes`

Funcion:

- Lista de estudiantes registrados.
- Busqueda.
- Creacion de estudiante nuevo.
- Edicion de estudiante.
- Eliminacion de estudiante.

Acciones:

- Alta de estudiante.
- Edicion de estudiante.
- Eliminacion con respaldo en `estudiantes_eliminados`.

#### `views/Admin/register_students.php`

Titulo: `Registrar Estudiante`

Funcion:

- Pantalla/formulario dedicado para registrar estudiantes.
- Carga nombre, correo, identificacion e imagen.

#### `views/Admin/update_students.php`

Funcion:

- Endpoint de actualizacion de estudiantes.
- Ejecuta `UPDATE` sobre tabla `estudiantes`.

### 7.4 Gestion de edificios

#### `views/Admin/register_buldings.php`

Titulo: `Registro de Edificios`

Funcion:

- Alta de edificios.
- Busqueda y filtrado por tipo.
- Vista de tarjetas o resumen de edificios registrados.
- Subida de imagen.

Campos observados en insercion:

- Nombre
- Codigo
- Pisos
- Cupo
- Direccion
- Tipo
- Descripcion
- Imagen
- Latitud
- Longitud

Uso practico:

- Es la pagina principal para crear edificios desde el rol administrador.

#### `views/Admin/table_build.php`

Titulo: `Ver Lista Edificios`

Funcion:

- Tabla completa de edificios.
- Busqueda.
- Eliminacion de edificios.
- Antes de eliminar un edificio, tambien elimina sus espacios asociados.

#### `views/Admin/update_building.php`

Titulo: `Actualizar Edificio`

Funcion:

- Vista detalle de un edificio.
- Permite actualizar descripcion, imagen y datos generales.
- Muestra espacios asociados al edificio.
- Tiene boton de regreso y modo de edicion visual.

### 7.5 Gestion de espacios

#### `views/Admin/vista_spaces.php`

Titulo: `Espacios`

Funcion:

- Lista de espacios de un edificio especifico mediante `edificio_id`.
- Permite crear nuevos espacios para ese edificio.
- Permite eliminar espacios.

Campos usados en insercion:

- Codigo
- Capacidad
- Tipo de espacio
- Descripcion general
- Imagen
- Edificio relacionado

#### `views/Admin/table_spaces.php`

Titulo: `Ver Lista Espacios`

Funcion:

- Tabla general de todos los espacios academicos.
- Permite busqueda y eliminacion.
- Muestra edificio asociado.

#### `views/Admin/update_spaces.php`

Titulo: `Actualizar Espacio - Admin`

Funcion:

- Vista de detalle del espacio para administrador.
- Permite editar descripcion, codigo, capacidad e imagen.
- Muestra edificio relacionado.
- Permite asignar equipamientos al espacio.
- Tambien contiene logica para registrar reportes de equipamiento y actualizar su estado.

Subfunciones importantes:

- Inserta relaciones en `espacios_equipamiento`.
- Usa `ON DUPLICATE KEY UPDATE` para actualizar cantidad/estado si la relacion ya existe.
- Muestra equipamientos vinculados al espacio.

#### `views/Admin/disponibilidad_spaces.php`

Titulo: `Disponibilidad del Espacio`

Funcion:

- Muestra la disponibilidad temporal de un espacio especifico.
- Consulta reservas existentes por rango de fecha/hora.
- Sirve como vista detallada del calendario logico del espacio.

### 7.6 Gestion de equipamientos

#### `views/Admin/equipment.php`

Titulo: `Registro de Edificios` pero contenido real: `Gestion de Equipamientos`

Funcion:

- Registro de equipamientos.
- Subida de imagen.
- Estado del equipamiento.
- Eliminacion de equipamientos.
- Listado general.

Campos observados:

- Nombre
- Codigo
- Descripcion
- Imagen
- Estado

#### `views/Admin/update_equipment.php`

Titulo: `Actualizar Equipamiento`

Funcion:

- Edita un equipamiento ya registrado.
- Permite cambiar descripcion, imagen, nombre, codigo y estado.

#### `views/Admin/table_equipament_spaces.php`

Titulo: `Lista Equipamientos Asociados`

Funcion:

- Lista las relaciones entre equipamientos y espacios academicos.
- Muestra edificio, espacio, equipamiento, cantidad y estado.
- Permite eliminar la relacion de asignacion.

### 7.7 Gestion de reservas

#### `views/Admin/table_reservation.php`

Titulo: `Ver Reservas`

Funcion:

- Lista reservas pendientes para revision administrativa.
- Permite busqueda.
- Resalta visualmente conflictos de horario.
- Permite aprobar o rechazar.
- Permite exportar informacion.
- Permite eliminar una reserva por POST.

Acciones:

- Aprobar: envia formulario a `approve_reservation.php`.
- Rechazar: usa `fetch` hacia `../../php/rechazar_reserva.php` con motivo.
- Exportar: envia a `../../php/export.php`.

Observaciones funcionales:

- Solo muestra reservas en estado `pendiente`.
- Calcula conflictos comparando cruces de horario en el mismo espacio.

#### `views/Admin/approve_reservation.php`

Funcion:

- Aprueba una reserva.
- Verifica si el usuario acepta notificaciones por correo.
- Revisa conflicto con otras reservas aceptadas.
- Actualiza el estado.
- Puede enviar correo con PHPMailer al docente.

#### `php/rechazar_reserva.php`

Funcion:

- Rechaza una reserva desde backend.
- Guarda `motivo_rechazo`.
- Envia notificacion por correo al docente.
- Responde en JSON para integracion con `fetch`.

### 7.8 Asistencias

#### `views/Admin/asistencias_docente.php`

Titulo: `Asistencias`

Funcion:

- Consulta administrativa de asistencias registradas por reservas.
- Resume total de asistencias y detalles asociados.

Uso:

- Es una vista de supervision, no de toma de asistencia.

### 7.9 Mensajeria y soporte

#### `views/Admin/messages.php`

Titulo interno: `Mensajes Recibidos`

Funcion:

- Buzon del administrador para solicitudes de docentes.
- Busca por fecha, prioridad y tipo.
- Permite responder un mensaje.
- Permite eliminar un mensaje.
- Marca mensajes resueltos.

Acciones:

- Responder: `fetch` a `responder_mensaje.php`.
- Eliminar: `fetch` a `delete_messages.php`.

#### `views/Admin/responder_mensaje.php`

Funcion:

- Endpoint AJAX.
- Guarda respuesta y cambia estado a `Resuelto`.
- Devuelve JSON.

#### `views/Admin/delete_messages.php`

Funcion:

- Endpoint AJAX.
- Elimina un mensaje por ID.
- Devuelve JSON.

### 7.10 Reportes de equipamiento

#### `views/Admin/table_equipment_reports.php`

Titulo: `Reportes de Equipamiento`

Funcion:

- Lista solicitudes pendientes de docentes sobre el estado de equipamientos.
- Permite buscar.
- Permite aprobar o rechazar reportes.

Acciones:

- Aprobar: `approve_equipment_report.php`.
- Rechazar: `rechazar_reporte_equipamiento.php` por `fetch`.

#### `views/Admin/approve_equipment_report.php`

Funcion:

- Marca la solicitud como aprobada.
- Actualiza estado del equipamiento relacionado en `espacios_equipamiento`.

#### `views/Admin/rechazar_reporte_equipamiento.php`

Funcion:

- Marca la solicitud como rechazada.
- Guarda motivo de rechazo.
- Devuelve JSON.

## 8. Vistas del docente

### 8.1 Dashboard y navegacion principal

#### `views/Docente/docente_dashboard.php`

Titulo: `Panel Docente`

Que muestra:

- Total de reservas del docente.
- Total de estudiantes en sistema.
- Grafico de estudiantes asociados a reservas por mes.
- Grafico de aulas mas utilizadas.
- Grafico de horas totales de uso por aula.
- Menu lateral con edificios, disponibilidad, mis reservas, asistencias, soporte, solicitudes y ajustes.
- Boton flotante de chatbot.

Chatbot:

- Interfaz modal incrustada en la vista.
- Envia preguntas a `../../php/chatbot.php`.

### 8.2 Exploracion de edificios y espacios

#### `views/Docente/vista_buildings.php`

Titulo: `Edificios`

Funcion:

- Muestra edificios disponibles para exploracion docente.
- Incluye buscador, filtros y mapa con ubicaciones cargadas desde `obtener_ubicaciones.php`.
- Es la vista principal para navegar a los espacios de un edificio.

Observacion:

- Aunque el docente no deberia crear edificios, esta vista conserva logica heredada de insercion; en la practica se comporta como vista de consulta/exploracion.

#### `views/Docente/table_build_docente.php`

Titulo: `Ver Lista Edificios`

Funcion:

- Tabla alternativa de edificios para docente.
- Parece una vista complementaria o heredada.

#### `views/Docente/update_building_docente.php`

Titulo: `Informacion del Edificio`

Funcion:

- Vista de detalle de un edificio concreto.
- Muestra informacion general del edificio.
- Cuenta espacios asociados.
- Boton de regreso.

#### `views/Docente/vista_spaces_docente.php`

Titulo: `Espacios`

Funcion:

- Lista los espacios pertenecientes a un edificio para el docente.
- Se alimenta con `edificio_id`.
- Sirve como paso intermedio antes de entrar al detalle del espacio.

#### `views/Docente/table_spaces_docente.php`

Titulo: `Ver Lista Espacios`

Funcion:

- Tabla global de espacios visibles para el docente.
- Vista secundaria o de apoyo.

### 8.3 Disponibilidad y reserva de espacios

#### `views/Docente/table_disponibilidad.php`

Titulo: `Espacios Academicos`

Funcion:

- Tabla de espacios con disponibilidad.
- Revisa reservas para determinar franjas ocupadas.
- Permite acceder a mas detalle del espacio y a su disponibilidad.

#### `views/Docente/ver_disponibilidad.php`

Titulo: `Disponibilidad del Espacio`

Funcion:

- Muestra disponibilidad detallada de un espacio puntual.
- Consulta las reservas ya registradas del espacio.

#### `views/Docente/update_spaces_docente.php`

Titulo: `Informacion del Espacio`

Funcion:

- Vista mas importante para el docente al momento de reservar.
- Muestra imagen, capacidad, edificio, codigo y descripcion del espacio.
- Tiene pestaña de informacion y pestaña de equipamiento.
- Permite abrir modal de reserva.
- Permite ver disponibilidad.
- Permite reportar el estado de equipamientos del espacio.

Formulario de reserva:

- Nombre del solicitante.
- Fecha y hora de inicio.
- Fecha y hora de fin.
- Tipo de reservacion: Clase, Reunion, Evento.
- Descripcion.
- Buscador de estudiantes para asociarlos a la reserva.
- Inserta en `reservaciones` y luego en `reservaciones_estudiantes`.
- Valida que al menos un estudiante sea agregado.
- Impide reservar en fechas pasadas.
- Verifica conflictos con reservas aceptadas.
- Guarda la reserva como `Pendiente`.

Formulario de reporte de equipamiento:

- Seleccion de estado: Disponible, En Mantenimiento, No Disponible.
- Descripcion del problema o novedad.
- Inserta solicitud en `solicitudes_reporte_docente`.

#### `views/Docente/update_reservation.php`

Titulo: `Editar Reserva`

Funcion:

- Permite revisar y editar una reserva existente del docente.
- Carga fecha, horas, tipo, descripcion, espacio y estudiantes asociados.
- Permite agregar o quitar estudiantes por buscador AJAX.
- Si la reserva fue rechazada, muestra el motivo de rechazo.
- Si el estado ya es `aceptada`, `asistencia` o `finalizado`, bloquea la edicion.
- Al guardar, actualiza la reserva y la regresa a estado `pendiente`.

Acciones extra:

- Si la reserva esta en `asistencia` o `finalizado`, muestra boton para descargar PDF.
- Usa `buscar_estudiantes.php` y `update_students_ajax.php`.

### 8.4 Mis reservas y asistencia

#### `views/Docente/mis_reservas.php`

Titulo: `Ver Mis Reservas`

Funcion:

- Lista las reservas del docente con paginacion.
- Permite buscar.
- Permite entrar a actualizar la reserva.
- Permite eliminar la reserva por AJAX.
- Cuando la hora actual cae entre inicio y fin de una reserva aceptada, la pasa a estado `asistencia`.
- Si la reserva esta en estado `asistencia`, aparece accion para tomar asistencia.

Estados observados en codigo:

- `pendiente`
- `aceptada`
- `rechazada`
- `asistencia`
- `finalizado`

#### `views/Docente/tomar_asistencia.php`

Titulo: `Tomar asistencia`

Funcion:

- Solo permite registrar asistencia si la reserva esta en estado `asistencia`.
- Lista estudiantes vinculados a la reserva.
- El docente marca checkbox por estudiante asistente.
- Guarda asistencia en `asistencia_reservas`.
- Cambia estado de la reserva a `finalizado`.
- Tiene boton alterno `Ningun estudiante asistio`.

#### `views/Docente/asistencias.php`

Titulo: `Asistencias`

Funcion:

- Vista docente para consultar asistencias registradas.
- Resume total de asistencias y detalle historico.

#### `views/Docente/reporte_reserva_pdf.php`

Funcion:

- Genera PDF de una reserva, incluyendo estudiantes y asistencia.
- Usa TCPDF.
- Se dispara desde `update_reservation.php` cuando la reserva ya paso por asistencia/finalizacion.

### 8.5 Soporte y solicitudes

#### `views/Docente/suport.php`

Titulo visual: `Enviar Queja o Comentario`

Funcion:

- Formulario de soporte del docente.
- Permite seleccionar prioridad: Baja, Media, Alta.
- Permite seleccionar tipo: Soporte, Desarrollo, Capacitacion.
- Calcula `tiempo_limite` automaticamente segun el tipo.
- Inserta en tabla `mensajes` con destinatario `admin`.

Tiempos definidos en codigo:

- Soporte: 72 horas.
- Desarrollo: 360 horas.
- Capacitacion: 48 horas.

#### `views/Docente/mis_solicitudes.php`

Titulo: `Ver Mis Solicitudes`

Funcion:

- Lista solicitudes de soporte creadas por el docente.
- Permite buscar, revisar y eliminar solicitudes propias.
- Tiene modal de detalle con respuesta del administrador y tiempo limite.

### 8.6 Utilidades AJAX y apoyo docente

#### `views/Docente/buscar_estudiantes.php`

Funcion:

- Endpoint JSON para autocompletar estudiantes por nombre.
- Se usa al crear o editar reservas.

#### `views/Docente/update_students_ajax.php`

Funcion:

- Endpoint AJAX para actualizar estudiantes de una reserva.
- En el codigo actual usa la tabla `reservas`, lo que sugiere un resto heredado y posible inconsistencia con la tabla `reservaciones`.

#### `views/Docente/obtener_ubicaciones.php`

Funcion:

- Devuelve nombre, direccion, latitud y longitud de edificios.
- Se usa para el mapa de `vista_buildings.php`.

## 9. Scripts PHP de soporte en `ClassPaneles/templates/php`

### Autenticacion y sesiones

#### `php/login_be.php`

- Login principal del sistema.
- Verifica credenciales en `usuarios`.
- Crea sesion `admin_session` o `docente_session` segun rol.
- Redirige al dashboard correspondiente.

#### `php/login_be_docente.php`

- Variante de login por rol docente.
- Actualmente es redundante frente a `login_be.php`.

#### `php/admin_session.php`

- Protege vistas de administrador.
- Valida que el rol sea `admin`.
- Carga nombre, imagen, rol e ID del usuario.

#### `php/docente_session.php`

- Protege vistas de docente.
- Valida que el rol sea `docente`.
- Guarda `id_usuario` en sesion.
- Carga imagen, nombre y rol.

#### `php/students_session.php`

- Intenta cargar datos de estudiante desde sesion.
- No tiene integracion visible con una interfaz de estudiante en el proyecto actual.

### Cierre de sesion

#### `php/cerrar_sesion.php`

- Cierra sesiones de admin y docente si existen.
- Redirige al login.

#### `php/cerrar_sesion_admin.php`

- Cierra solo la sesion de administrador.

#### `php/cerrar_session_docente.php`

- Cierra solo la sesion de docente.

### Configuracion de cuenta

#### `php/config.php`

- Pagina de configuracion del administrador.
- Muestra datos de cuenta.
- Permite habilitar modo actualizacion y guardar cambios.
- Reutiliza `update_table.php`.

#### `php/config_docente.php`

- Pagina de configuracion del docente.
- Muestra datos de cuenta.
- Permite modificar datos y activar/desactivar `notificaciones_email`.
- Reutiliza `update_table.php`.

#### `php/update_table.php`

- Actualiza datos de usuario en tabla `usuarios`.
- Soporta cambio de imagen.
- Si cambia el correo del usuario autenticado, actualiza tambien la sesion.

#### `php/update_table_students.php`

- Actualiza datos de estudiantes.
- Soporta imagen.

### Registros y consultas

#### `php/registro_usuario_be.php`

- Inserta nuevos usuarios en `usuarios`.
- Valida correo y usuario duplicados.
- Hashea contrasena con `sha512`.

#### `php/registro_estudiante_be.php`

- Inserta nuevos estudiantes en `estudiantes`.
- Soporta imagen.

#### `php/get_user.php`

- Endpoint para obtener informacion de un usuario por ID.
- Se usa para precargar formularios o modales.

#### `php/get_students.php`

- Endpoint para obtener informacion de un estudiante por ID.
- Uso similar a `get_user.php`.

#### `php/get_stats.php`

- Devuelve JSON con totales.
- En su forma actual depende de variables no definidas dentro del archivo, por lo que parece incompleto o heredado.

### Exportacion y reportes

#### `php/export.php`

- Exporta informacion de reservas.
- Admite salidas tipo Excel, PDF e imagen PNG desde la interfaz de reservas.
- Consulta tambien metricas agregadas.

### Correo y notificaciones

#### `php/rechazar_reserva.php`

- Rechaza reservas y envia correo con motivo.

#### `php/send_reset_link.php`

- Envia enlace de recuperacion.

### Chatbot

#### `php/chatbot.php`

- Asistente virtual textual para docentes.
- Atiende saludos, preguntas sobre reservas, codigos de aula y fechas.
- Trabaja sobre la base de consultas al sistema.
- Se invoca desde el dashboard docente.

### Conexion y utilitarios menores

#### `php/conexion_be.php`

- Conexion `mysqli` a base de datos `login_register_db` en localhost.

#### `php/info.php`

- Archivo minimo sin funcionalidad aparente.

#### `php/info_autoload.php`

- Archivo corto de apoyo para autoload; no participa en flujo de usuario final.

## 10. Assets y recursos del proyecto

### CSS en `assets/css`

- `style.css`: estilos del login.
- `style_panel.css`: hoja principal de paneles admin/docente.
- `style_building.css`: estilos de vistas de edificios y formularios amplios.
- `update_style.css`: estilos de paginas de actualizacion/detalle.
- `style_paneles.css`, `style_space_docente.css`, `style_teacher.css`, `styles.php`: estilos o archivos secundarios/legados.

### JavaScript en `assets/js`

Se encontraron archivos duplicados tambien en `templates/js/`.

Principales:

- `button_update.js`: habilita/deshabilita formularios de edicion.
- `script_menu.js`: comportamiento del menu lateral y dropdowns.
- `main.js`: login y comportamiento general inicial.
- `script_modal.js`: manejo de modales.
- `script_stats.js`: soporte visual para estadisticas.
- `building_edit.js`, `update_user_modal.js`: apoyo para interacciones puntuales.

### Imagenes en `assets/images`

Recursos principales:

- Logos (`logo1.png`, `logo2.png`, `logo_correo.png`).
- `chatbot.png`.
- `loader.gif`.
- Iconografia visual de editar, eliminar, aceptar, rechazar, configuracion, etc.
- Imagenes por defecto para edificios y fondos.

### Uploads en `uploads`

- Guarda imagenes de usuarios, estudiantes, edificios y equipamientos cargadas desde formularios.
- Tambien contiene imagenes de ejemplo y recursos ya subidos en el sistema.

## 11. Carpetas externas o no funcionales para el usuario final

### `fullcalendar-6.1.15/`

- Libreria externa de calendario.
- Contiene distribuciones, ejemplos, locales y paquetes.
- No corresponde a paginas del negocio principal, sino a una dependencia del proyecto.

### `vendor/`

- Dependencias Composer.
- No son paginas de usuario, pero si componentes internos clave para correo y PDF.

## 12. Inventario completo de paginas y endpoints del proyecto

### Paginas visibles para login y cuenta

- `ClassPaneles/templates/index.php`
- `ClassPaneles/templates/php/forgot_password.php`
- `ClassPaneles/templates/php/reset_password.php`
- `ClassPaneles/templates/php/config.php`
- `ClassPaneles/templates/php/config_docente.php`

### Vistas visibles del administrador

- `views/Admin/admin_dashboard.php`
- `views/Admin/vista_cuentas.php`
- `views/Admin/create_account.php`
- `views/Admin/update_user.php`
- `views/Admin/vista_students.php`
- `views/Admin/register_students.php`
- `views/Admin/update_students.php`
- `views/Admin/register_buldings.php`
- `views/Admin/table_build.php`
- `views/Admin/update_building.php`
- `views/Admin/vista_spaces.php`
- `views/Admin/table_spaces.php`
- `views/Admin/update_spaces.php`
- `views/Admin/disponibilidad_spaces.php`
- `views/Admin/equipment.php`
- `views/Admin/update_equipment.php`
- `views/Admin/table_equipament_spaces.php`
- `views/Admin/table_reservation.php`
- `views/Admin/approve_reservation.php`
- `views/Admin/asistencias_docente.php`
- `views/Admin/messages.php`
- `views/Admin/responder_mensaje.php`
- `views/Admin/delete_messages.php`
- `views/Admin/table_equipment_reports.php`
- `views/Admin/approve_equipment_report.php`
- `views/Admin/rechazar_reporte_equipamiento.php`

### Vistas visibles del docente

- `views/Docente/docente_dashboard.php`
- `views/Docente/vista_buildings.php`
- `views/Docente/table_build_docente.php`
- `views/Docente/update_building_docente.php`
- `views/Docente/vista_spaces_docente.php`
- `views/Docente/table_spaces_docente.php`
- `views/Docente/table_disponibilidad.php`
- `views/Docente/ver_disponibilidad.php`
- `views/Docente/update_spaces_docente.php`
- `views/Docente/mis_reservas.php`
- `views/Docente/update_reservation.php`
- `views/Docente/tomar_asistencia.php`
- `views/Docente/asistencias.php`
- `views/Docente/reporte_reserva_pdf.php`
- `views/Docente/suport.php`
- `views/Docente/mis_solicitudes.php`

### Endpoints auxiliares del docente

- `views/Docente/buscar_estudiantes.php`
- `views/Docente/update_students_ajax.php`
- `views/Docente/obtener_ubicaciones.php`

### Scripts backend generales

- `php/admin_session.php`
- `php/cerrar_sesion.php`
- `php/cerrar_sesion_admin.php`
- `php/cerrar_session_docente.php`
- `php/chatbot.php`
- `php/conexion_be.php`
- `php/docente_session.php`
- `php/export.php`
- `php/get_stats.php`
- `php/get_students.php`
- `php/get_user.php`
- `php/login_be.php`
- `php/login_be_docente.php`
- `php/rechazar_reserva.php`
- `php/registro_estudiante_be.php`
- `php/registro_usuario_be.php`
- `php/send_reset_link.php`
- `php/students_session.php`
- `php/update_password.php`
- `php/update_table.php`
- `php/update_table_students.php`
- `php/info.php`
- `php/info_autoload.php`

## 13. Tablas y entidades que se infieren desde el codigo

A partir de las consultas SQL, el sistema parece trabajar con estas tablas principales:

- `usuarios`
- `estudiantes`
- `edificios`
- `espacios_academicos`
- `equipamiento`
- `espacios_equipamiento`
- `reservaciones`
- `reservaciones_estudiantes`
- `asistencia_reservas`
- `mensajes`
- `estadisticas`
- `usuarios_eliminados`
- `estudiantes_eliminados`
- `solicitudes_reporte_docente`
- `reportes_equipamiento` en partes heredadas del codigo

## 14. Recomendaciones de uso operativo

### Para administradores

- Crear primero edificios y luego espacios.
- Asignar equipamientos una vez creados los espacios.
- Revisar frecuentemente `Reservas` y `Buzon ayuda`.
- Usar exportaciones para reportes institucionales.
- Revisar `Reportes de Equipamiento` para mantener actualizado el estado real de recursos.

### Para docentes

- Antes de reservar, abrir el detalle del espacio y revisar `Disponibilidad`.
- Agregar siempre al menos un estudiante en la reserva.
- Si una reserva es rechazada, revisar el motivo en `Editar Reserva`.
- Tomar asistencia cuando la reserva este en estado `asistencia`.
- Usar `Mis solicitudes` para seguir respuestas del administrador.

## 15. Observaciones detectadas durante la revision

Estas notas no impiden entender el proyecto, pero conviene tenerlas presentes:

- Hay textos con problemas de codificacion de caracteres, por ejemplo `ConfiguraciÃ³n` o `ContraseÃ±a`.
- Conviven nombres `ClassTrack` y `Unispace`.
- Algunas vistas docentes conservan logica heredada de creacion que no coincide con el rol real esperado.
- `update_students_ajax.php` parece apuntar a una tabla `reservas` en vez de `reservaciones`.
- Existen archivos alternos o heredados que duplican funciones, como `login_be_docente.php` o vistas tipo `table_*` y `vista_*` para contenido parecido.
- El archivo `.env` contiene configuracion SMTP sensible y debe manejarse con cuidado fuera de entornos locales.

## 16. Conclusion

El proyecto actual de ClassTrack ya cubre un flujo completo de operacion universitaria para reservas de espacios, asistencia, soporte y control de equipamiento. El nucleo funcional esta dividido con claridad entre:

- login y sesiones,
- panel administrativo,
- panel docente,
- endpoints de soporte,
- exportacion y notificaciones.

Este documento puede usarse como manual de navegacion del sistema y tambien como mapa funcional del codigo para mantenimiento, induccion o auditoria interna.
