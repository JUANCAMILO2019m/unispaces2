# Manual de Usuario - ClassTrack

**Guía paso a paso para estudiantes, docentes y administradores**

Este manual le ayudará a entender cómo usar ClassTrack de manera fácil y rápida. Encontrará instrucciones detalladas para cada cosa que pueda hacer en el sistema.

---

## Sección 1: Cómo Ingresar al Sistema

### 1.1 Primer inicio en ClassTrack
1. Abra su navegador (Chrome, Firefox, Edge, etc.).
2. Escriba en la barra de direcciones: `localhost/ClassPaneles/templates/index.php` (o la dirección que su institución le haya proporcionado).
3. Debe ver una página con dos campos: **Correo** y **Contraseña**.

### 1.2 Iniciar sesión
1. **Escriba su correo electrónico** en el primer campo (el correo con el que se registró).
2. **Escriba su contraseña** en el segundo campo.
3. Haga clic en el botón **"Iniciar sesión"** (color azul, grande).
4. Espere a que el sistema cargue (2-3 segundos).
5. Se abrirá automáticamente su panel principal.

**¿Qué verá después?**
- Si es **Administrador**: entrará al panel de control con opciones para gestionar edificios, espacios y usuarios.
- Si es **Docente**: entrará a su panel personal con opciones para ver espacios disponibles y hacer reservas.

### 1.3 Si olvidó su contraseña
1. En la página de login, busque el enlace **"¿Olvidaste tu contraseña?"** (color gris, abajo del botón).
2. Haga clic.
3. Escriba el correo con el que se registró.
4. Haga clic en **"Enviar enlace de recuperación"**.
5. **Revise su correo** (puede tardar 1-2 minutos). Busque en la bandeja de spam si no lo ve.
6. Haga clic en el enlace del correo.
7. **Escriba su nueva contraseña** dos veces (debe ser igual en ambos campos).
8. Haga clic en **"Cambiar contraseña"**.
9. Vuelva al login e inicie con su nueva contraseña.

### 1.4 Cierre seguro de sesión
- En cualquier sección, busque **"Cerrar sesión"** o **"Salir"** en el menú.
- Haga clic para terminar su sesión.
- Esto protege su cuenta si comparte la computadora.

---

## Sección 2: Panel del Administrador (Gestión de Edificios y Espacios)

### 2.1 Acceso y menú principal
Una vez ingrese como administrador, verá un **menú en el lado izquierdo** con las opciones:
- Inicio
- Cuentas
- Estudiantes
- Edificios
- Añadir Edificios
- Espacios
- Equipamientos
- Reservas
- Mensajes
- Configuración

### 2.2 Crear un nuevo edificio
1. Haga clic en **"Añadir Edificios"** en el menú izquierdo.
2. Verá un formulario con campos vacíos.
3. **Rellene lo siguiente**:
   - **Nombre del edificio**: ej: "Torre A", "Edificio de Ingeniería".
   - **Tipo**: seleccione Laboratorio, Académico, Auditorio, etc.
   - **Ubicación/Dirección**: ej: "Carrera 5 # 20-30".
   - **Número de pisos**: escriba cuántos pisos tiene.
   - **Descripción** (opcional): agregue detalles, ej: "Edificio recién remodelado".
4. Haga clic en **"Guardar"** o **"Crear edificio"**.
5. Verá un mensaje confirmando que fue creado.

### 2.3 Ver y editar edificios
1. Haga clic en **"Edificios"** en el menú.
2. Verá una **tabla con todos los edificios** que ha creado.
3. Para buscar un edificio específico, use la **barra de búsqueda** arriba de la tabla.
4. Para **editar** información: busque el botón **"Editar"** (icono de lápiz) en la fila del edificio y haga clic.
5. Modifique los datos que necesite.
6. Haga clic en **"Guardar cambios"**.
7. Para **eliminar** un edificio: busque el botón **"Eliminar"** (icono de basurero) y haga clic (se le pedirá confirmación).

### 2.4 Añadir espacios a un edificio
1. Haga clic en **"Espacios"** en el menú.
2. Verá la lista de edificios. **Seleccione el edificio** donde desea agregar espacios.
3. Haga clic en **"Agregar nuevo espacio"**.
4. **Rellene el formulario**:
   - **Código del espacio**: ej: "A101", "LAB-02" (código único).
   - **Nombre**: ej: "Aula de sistemas", "Laboratorio de química".
   - **Tipo**: Aula, Laboratorio, Auditorio, etc.
   - **Capacidad**: número máximo de personas (ej: 30, 50).
5. **Opcionalmente, suba una foto** del espacio:
   - Haga clic en **"Seleccionar imagen"**.
   - Busque en su computadora una foto en formato JPG o PNG.
   - Haga clic en **"Abrir"**.
6. Haga clic en **"Crear espacio"**.
7. El espacio aparecerá en la lista del edificio.

### 2.5 Editar o eliminar espacios
1. En la lista de espacios, busque el espacio que desea editar.
2. Haga clic en **"Editar"** para cambiar datos (código, nombre, capacidad, tipo).
3. Haga clic en **"Guardar cambios"**.
4. Para **eliminar**: haga clic en **"Eliminar"**, confirme.

### 2.6 Gestionar equipamiento
1. Haga clic en **"Equipamientos"** en el menú.
2. Verá la lista de equipos (proyectores, computadoras, etc.).
3. **Para agregar equipamiento**:
   - Haga clic en **"Nuevo equipamiento"**.
   - Nombre: ej: "Proyector Epson", "Computadora Dell".
   - Tipo: Proyector, Computadora, Pizarra inteligente, etc.
   - Cantidad: cuántas unidades tiene.
   - Haga clic en **"Guardar"**.
4. **Para asignar equipamiento a un espacio**:
   - En la lista de equipamiento, busque el equipo.
   - Haga clic en **"Asignar a espacio"**.
   - Seleccione el espacio de la lista desplegable.
   - Indique cuántas unidades asigna.
   - Haga clic en **"Asignar"**.

---

## Sección 3: Panel del Administrador (Gestión de Usuarios y Reservas)

### 3.1 Ver y gestionar cuentas de usuario
1. Haga clic en **"Cuentas"** en el menú.
2. Verá una **tabla con todos los usuarios**: nombre, correo, rol (admin/docente), estado.
3. **Para buscar un usuario**: use la barra de búsqueda escribiendo el nombre o correo.
4. **Para cambiar el rol de un usuario**:
   - Busque el usuario en la tabla.
   - Haga clic en **"Editar"**.
   - En el campo **"Rol"**, cambie a Admin o Docente.
   - Haga clic en **"Guardar"**.
5. **Para desactivar una cuenta** (si el usuario no debe entrar más):
   - Haga clic en **"Desactivar"** (o cambie estado a "Inactivo").
   - Confirme.
6. **Para reactivar**: haga clic en **"Activar"**.

### 3.2 Ver estudiantes registrados
1. Haga clic en **"Estudiantes"** en el menú.
2. Verá una lista de todos los estudiantes del sistema.
3. Puede **buscar** por nombre o código de estudiante.
4. **Para exportar la lista a Excel**:
   - Busque el botón **"Descargar en Excel"** o **"Exportar"**.
   - El archivo se descargará en su computadora automáticamente.

### 3.3 Ver y gestionar reservas
1. Haga clic en **"Reservas"** en el menú.
2. Verá la **tabla de todas las reservas** solicitadas por docentes.
3. Columnas: Docente, Espacio, Fecha, Estado (Pendiente/Aceptada/Rechazada).

**¿Qué significa cada estado?**
- **Pendiente**: Docente solicitó pero aún no usted aprueba.
- **Aceptada**: Usted ya aprobó, el docente puede usar el espacio.
- **Rechazada**: Usted rechazó la solicitud (el docente no puede usar ese espacio esa fecha).

### 3.4 Aprobar una reserva
1. En la tabla de reservas, **busque una reserva con estado "Pendiente"**.
2. Haga clic en **"Ver detalles"** o **"Expandir"**.
3. Revise: fecha, hora, espacio, cantidad de estudiantes.
4. Si puede aprobar:
   - Haga clic en **"Aceptar"** o **"Aprobar"**.
   - El estado cambia a "Aceptada".
   - El docente recibe notificación.
5. Si no puede aprobar (conflicto de horarios, espacio no disponible):
   - Haga clic en **"Rechazar"**.
   - Se abre un cuadro para escribir el motivo. Escriba algo como: "Espacio ocupado a esa hora" o "Capacidad insuficiente".
   - Haga clic en **"Enviar rechazo"**.

### 3.5 Mensajes de soporte técnico
1. Haga clic en **"Mensajes"** en el menú.
2. Verá los mensajes que docentes le han enviado pidiendo ayuda.
3. Cada mensaje muestra: Remitente, Tipo (Soporte/Desarrollo/Capacitación), Prioridad (Baja/Media/Alta).
4. **Para responder un mensaje**:
   - Haga clic en el mensaje.
   - Se abre el detalle con la descripción del problema.
   - Escriba su respuesta en el campo de abajo.
   - Haga clic en **"Responder"**.
   - El docente recibe su respuesta.
5. **Para marcar como resuelto**: haga clic en **"Marcar como resuelto"** cuando termine de ayudar.

---

## Sección 4: Panel del Docente (Consulta y Reservas)

### 4.1 Acceso al panel docente
1. Inicie sesión con su usuario docente.
2. Se abre automáticamente su **panel personal**.
3. En el lado izquierdo verá el menú con opciones:
   - Inicio
   - Edificios
   - Disponibilidad
   - Mis reservas
   - Asistencias
   - Soporte técnico
   - Mis solicitudes
   - Ajustes
   - Cerrar sesión

### 4.2 Ver el panel de inicio (Dashboard)
1. Cuando ingresa, está en la pestaña **"Inicio"**.
2. Aquí verá un **resumen personal**:
   - Total de reservas que ha hecho.
   - Horas totales que ha usado espacios.
   - Aulas más usadas por usted.
   - Gráfico mostrando su actividad del mes.
3. Esta información **solo muestra sus datos**, no los de otros docentes.

### 4.3 Ver edificios disponibles
1. Haga clic en **"Edificios"** en el menú.
2. Verá una lista o tarjetas con todos los edificios de la institución.
3. Cada tarjeta muestra: foto (si la hay), nombre, tipo, ubicación.
4. **Para buscar un edificio específico**: use la barra de búsqueda escribiendo el nombre.
5. **Para filtrar por tipo**: seleccione en el desplegable (Académico, Laboratorio, Auditorio, etc.).
6. **Para ver espacios de un edificio**: haga clic en **"Ver espacios"** o **"Expandir"**.
   - Se mostrará la lista de aulas/laboratorios en ese edificio.

### 4.4 Ver disponibilidad de espacios
1. Haga clic en **"Disponibilidad"** en el menú.
2. Verá una **tabla con todos los espacios disponibles HOY**.
3. Columnas: Foto, Código, Nombre, Tipo, Capacidad, Horarios libres.
4. El sistema **calcula automáticamente** qué horas están libres basado en otras reservas.
5. **Ejemplo**: Si una aula tiene clases de 8 a 10, y de 11 a 1, verá que está disponible 10-11 y después de 1.

### 4.5 Crear una nueva reserva
**Opción 1: Desde disponibilidad**
1. En la tabla de disponibilidad, **busque un espacio que le guste**.
2. Haga clic en **"Reservar"** en esa fila.
3. Se abre un formulario.

**Opción 2: Desde edificios**
1. Vaya a **"Edificios"**.
2. Seleccione el edificio.
3. Haga clic en un espacio.
4. Haga clic en **"Reservar"**.

**Llenar el formulario de reserva**:
1. **Fecha de inicio**: seleccione de un calendario. Hoy, mañana o días futuros.
2. **Hora de inicio**: escriba la hora (ej: 8:00).
3. **Hora de fin**: escriba hasta qué hora necesita (ej: 10:00).
4. **Descripción**: escriba para qué es (ej: "Clase de cálculo", "Práctica de laboratorio").
5. **Número de estudiantes**: escriba cuántos estudiantes vendrán.
6. **Revisar datos**: verifique que todo esté correcto.
7. Haga clic en **"Enviar solicitud de reserva"** o **"Reservar"**.
8. Verá un mensaje **"Su solicitud fue enviada"**. El administrador la revisará.

### 4.6 Mis reservas - Ver estado
1. Haga clic en **"Mis reservas"** en el menú.
2. Verá una **tabla de todas sus reservas** pasadas y futuras.
3. Columnas: Espacio, Edificio, Fecha, Hora, Estado.

**Estados que puede ver**:
- **Pendiente**: está esperando que el admin apruebe.
- **Aceptada**: admin aprobó ✓ (puede usar el espacio).
- **Rechazada**: admin dijo que no (verá un motivo).
- **En uso**: ya marcó asistencia cuando usó el espacio.
- **Completada**: terminó y pasó la fecha.

### 4.7 Editar una reserva
1. En **"Mis reservas"**, busque la reserva que desea cambiar.
2. Solo puede editar si estado es **"Pendiente"** o **"Aceptada"** (y aún no pasó la fecha).
3. Haga clic en **"Editar"**.
4. Se abre el formulario de nuevo con los datos.
5. Cambie lo que necesite: horario, fecha, descripción.
6. Haga clic en **"Guardar cambios"**.
7. Si cambió horario u otros datos importantes, volverá a estado **"Pendiente"** para que admin revise de nuevo.

### 4.8 Cancelar una reserva
1. En **"Mis reservas"**, busque la reserva.
2. Haga clic en **"Cancelar"**.
3. Aparecerá una ventana pidiendo confirmación: "¿Está seguro de cancelar?"
4. Haga clic en **"Sí, cancelar"**.
5. La reserva se marca como **"Cancelada"**. El horario queda libre para otros.

### 4.9 Marcar asistencia (Check-in)
Cuando llega al espacio reservado y lo usa:
1. Haga clic en **"Mis reservas"**.
2. Busque la reserva de **hoy** con estado **"Aceptada"**.
3. Haga clic en **"Marcar asistencia"** o **"Check-in"**.
4. Confirme.
5. Estado cambia a **"En uso"**.
6. Esto registra que usted realmente usó el espacio.

### 4.10 Enviar mensaje de soporte técnico
Si tiene un problema o pregunta:
1. Haga clic en **"Soporte técnico"** en el menú.
2. Se abre un formulario con campos:
   - **Tipo**: seleccione Soporte (problema), Desarrollo (nueva función), o Capacitación (no sé usar algo).
   - **Prioridad**: seleccione Baja (no es urgente), Media (dentro de días), Alta (muy urgente).
   - **Descripción**: escriba en detalle qué necesita. Ej: "No puedo crear reservas en Disponibilidad", "Necesito ayuda para exportar datos".
3. Haga clic en **"Enviar solicitud"**.
4. El administrador recibirá su mensaje y le responderá.

### 4.11 Ver mis solicitudes de soporte
1. Haga clic en **"Mis solicitudes"** en el menú.
2. Verá el historial de todos los mensajes que ha enviado.
3. Cada uno muestra: tipo, prioridad, descripción, estado (Pendiente, Respondido, Resuelto).
4. Haga clic en una solicitud para ver la respuesta del admin.

### 4.12 Cambiar contraseña y ajustes personales
1. Haga clic en **"Ajustes"** en el menú.
2. Aparecerá un formulario con:
   - **Nombre**: su nombre (puede editar).
   - **Correo**: su correo (puede editar).
   - **Teléfono** (opcional): agregue un teléfono de contacto.
3. **Para cambiar contraseña**:
   - Haga clic en **"Cambiar contraseña"** (o similar).
   - Escriba su **contraseña actual**.
   - Escriba su **nueva contraseña**.
   - Escríbala de nuevo para confirmar.
   - Haga clic en **"Guardar cambios"**.

---

## Sección 5: Flujo recomendado paso a paso

### 5.1 Para un docente NEW en el sistema
1. **Visite Edificios**: explore qué espacios hay en la institución.
2. **Visite Disponibilidad**: vea qué espacios están libres hoy y mañana.
3. **Cree su primera reserva**: desde Disponibilidad, seleccione un espacio y horario.
4. **Espere aprobación**: en Mis reservas verá si el admin aprobó.
5. **Si aprobó**: use el espacio en la fecha/hora. Cuando llegue, marque asistencia.
6. **Si rechazó**: lea el motivo y intente con otro espacio/horario.

### 5.2 Para un administrador que inicia
1. **Registre edificios**: Añadir Edificios > complete datos.
2. **Añada espacios**: Espacios > seleccione edificio > Agregar espacio > complete datos.
3. **Asigne equipamiento**: Equipamientos > asigne proyectores, computadoras, etc. a espacios.
4. **Vea cuentas**: Cuentas > verifique que usuarios tengan roles correctos.
5. **Revise reservas pendientes**: Reservas > apruebe o rechace solicitudes de docentes.
6. **Responda mensajes**: Mensajes > lea y responda peticiones de ayuda.

---

## Sección 6: Preguntas Frecuentes (FAQ)

**P: ¿Qué hago si no puedo ingresar?**
R: Verifique que escribió correo y contraseña correctos. Si olvidó contraseña, use "¿Olvidaste tu contraseña?". Si sigue sin funcionar, contacte al administrador.

**P: ¿Puedo ver reservas de otros docentes?**
R: No. Los docentes solo ven sus propias reservas. Los administradores ven todas.

**P: ¿Qué pasa si cancelo una reserva?**
R: El espacio se libera y otro docente puede reservarlo en ese horario.

**P: ¿Puedo cambiar una reserva después de enviarla?**
R: Sí, si aún no fue aprobada, está en estado "Pendiente" y puedo editarla. Si ya fue aprobada, también puedo editar si la fecha no pasó.

**P: ¿Cuánto tarda la aprobación de una reserva?**
R: Depende del administrador. Lo normal es 1-2 horas. Si es urgente, envíe un soporte técnico con prioridad Alta.

**P: ¿Puedo usar un espacio sin hacer reserva?**
R: No. Debe hacer reserva primero y el administrador debe aprobar.

---

## Sección 7: Consejos de uso

1. **Revise disponibilidad antes de reservar**: Vaya a "Disponibilidad" para ver horas libres.
2. **Sea descriptivo**: En la descripción de reserva, sea claro: "Clase de cálculo diferencial, 35 estudiantes".
3. **Reserve con tiempo**: No espere el último día. Haga reservas con días de anticipación.
4. **Marque asistencia**: Si va a usar el espacio, marque asistencia. Así queda registro de uso.
5. **Cierre sesión**: Cuando termine, haga clic en "Cerrar sesión" para proteger su cuenta.

---

## Conclusión

ClassTrack le permite gestionar espacios de forma organizada y fácil. Si sigue los pasos de este manual, podrá:
- **Docentes**: Consultar espacios, crear reservas y monitorear su estado.
- **Administradores**: Crear infraestructura, aprobar reservas y gestionar usuarios.

¿Tiene dudas? Envíe un mensaje de soporte técnico desde el panel.
