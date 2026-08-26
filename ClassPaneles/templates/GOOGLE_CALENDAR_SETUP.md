# Google Calendar en ClassTrack

Al solicitar una reserva, el docente puede añadirla a su calendario de Google. El evento se crea como `Solicitud pendiente` porque requiere aprobación administrativa.

1. Crea un proyecto en Google Cloud y habilita **Google Calendar API**.
2. Configura la pantalla de consentimiento OAuth y crea un cliente OAuth de tipo **Web application**.
3. Registra la URL de callback de tu instalación como URI de redirección autorizada.
4. Añade estas variables al `.env` de la raíz:

```env
GOOGLE_CALENDAR_CLIENT_ID=
GOOGLE_CALENDAR_CLIENT_SECRET=
GOOGLE_CALENDAR_REDIRECT_URI=http://localhost/ClassTrack/ClassPaneles/templates/views/Docente/google_calendar_callback.php
```

En producción usa HTTPS y el dominio real. No subas estas credenciales a Git.

La integración solicita solo el permiso `https://www.googleapis.com/auth/calendar.events` para crear eventos en el calendario del docente.
