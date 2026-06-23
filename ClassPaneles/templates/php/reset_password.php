<?php
session_start();

include 'conexion_be.php';

// Generar token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validar existencia del token
$token = $_GET['token'] ?? '';

// Validar formato del token (64 caracteres hexadecimales)
if (
    empty($token) ||
    !preg_match('/^[a-f0-9]{64}$/', $token)
) {
    echo '<script>
            alert("Token inválido.");
            window.location="../index.php";
          </script>';
    exit();
}

// Consulta preparada
$stmt = $conexion->prepare("
    SELECT id
    FROM usuarios
    WHERE reset_token = ?
    AND reset_expira > NOW()
    LIMIT 1
");

if (!$stmt) {
    die("Error SQL: " . $conexion->error);
}

$stmt->bind_param("s", $token);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Unispace</title>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet"
        href="../templates/assets/css/style.css">

    <link rel="shortcut icon"
        href="../templates/assets/images/logo1.png">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "DM Sans", sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .recovery-container {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .recovery-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1dc0c9, #1a93a0);
        }

        .icon-container {
            width: 70px;
            height: 70px;
            background: rgba(29, 192, 201, 0.1);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1.5rem;
        }

        .icon-container i {
            font-size: 2rem;
            color: #1dc0c9;
        }

        h1 {
            color: #2d3748;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .description {
            color: #718096;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: #1dc0c9;
            background: white;
            box-shadow: 0 0 0 3px rgba(29, 192, 201, 0.1);
        }

        button {
            width: 100%;
            padding: 1rem;
            background: #1dc0c9;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        button:hover {
            background: #1a93a0;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(29, 192, 201, 0.15);
        }

        button:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            color: #718096;
            text-decoration: none;
            font-size: 0.9rem;
            display: block;
        }

        .back-link:hover {
            color: #1dc0c9;
        }

        @media (max-width: 480px) {
            .recovery-container {
                padding: 1.5rem;
            }

            .icon-container {
                width: 60px;
                height: 60px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
        .password-strength-container {
                margin: 15px 0;
        }

        .strength-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 20px;
            transition: all .4s ease;
        }

        .strength-text {
            margin-top: 8px;
            font-size: .9rem;
            font-weight: 600;
            text-align: right;
            text-align:center;
        }
        .match-message {
            display: none;
            align-items: center;
            gap: 12px;
            margin-top: 15px;
            margin-bottom: 15px;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all .3s ease;
        }

        .match-message i {
            font-size: 1.2rem;
        }

        .match-success {
            display: flex;
            background: rgba(25, 135, 84, 0.08);
            border: 1px solid rgba(25, 135, 84, 0.2);
            color: #198754;
        }

        .match-error {
            display: flex;
            background: rgba(220, 53, 69, 0.08);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .match-content {
            display: flex;
            flex-direction: column;
        }

        .match-title {
            font-weight: 700;
        }

        .match-subtitle {
            font-size: 0.85rem;
            opacity: .8;
        }
        .nota-style {
            font-size: 0.8rem;
            color: #6c757d;
            text-align: left;
            margin-top: 10px;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0 20px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #d1d5db;
        }

        .divider span {
            margin: 0 15px;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .back-link {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1dc0c9;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link i {
            transition: transform 0.3s ease;
        }

        .back-link:hover {
            color: #1a93a0;
        }

        .back-link:hover i {
            transform: translateX(-4px);
        }
    </style>
</head>

<body>
<div class="recovery-container">
        <div class="icon-container">
            <i class="fas fa-lock"></i>
        </div>
        <h1>Restablece Tu Contraseña</h1>
    <form action="update_password.php" method="POST" id="resetForm">

    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="input-group">
        <i class="fas fa-lock"></i>

        <input
            type="password"
            id="new_password"
            name="new_password"
            placeholder="Nueva contraseña"
            autocomplete="new-password"
            required>
    </div>

    <div class="password-strength-container">

        <div class="strength-bar">
            <div class="strength-fill" id="strengthFill"></div>
        </div>

        <div class="strength-text" id="strengthText">
            Débil
        </div>

    </div>  

    <div class="input-group" style="margin-top:20px;">
        <i class="fas fa-lock"></i>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            placeholder="Confirmar contraseña"
            autocomplete="new-password"
            required>
    </div>

    <div id="matchMessage" class="match-message"></div>

    <button type="submit">
        Restablecer Contraseña
    </button>

    <p class="nota-style">
        Nota: La contraseña debe tener al menos 8 caracteres, 
        incluyendo una letra mayúscula, una letra minúscula, 
        un número y un carácter especial.
    <p>
    <div class="divider">
        <span>o</span>
    </div>

    <a href="../index.php" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Volver al inicio de sesión
    </a>
</div>
</form>

    <script>
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const password = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        const lengthReq = document.getElementById('length');
        const upperReq = document.getElementById('uppercase');
        const lowerReq = document.getElementById('lowercase');
        const numberReq = document.getElementById('number');
        const specialReq = document.getElementById('special');

        const strength = document.getElementById('passwordStrength');
        const matchMessage = document.getElementById('matchMessage');
        const form = document.getElementById('resetForm');

        function validatePassword() {

            let score = 0;

            const pass = password.value;

            if (pass.length >= 8) score++;
            if (/[A-Z]/.test(pass)) score++;
            if (/[a-z]/.test(pass)) score++;
            if (/[0-9]/.test(pass)) score++;
            if (/[^A-Za-z0-9]/.test(pass)) score++;

            switch(score){

                case 0:
                case 1:
                    strengthFill.style.width = "20%";
                    strengthFill.style.background = "#dc3545";
                    strengthText.textContent = "Muy débil";
                    strengthText.style.color = "#dc3545";
                    break;

                case 2:
                    strengthFill.style.width = "40%";
                    strengthFill.style.background = "#fd7e14";
                    strengthText.textContent = "Débil";
                    strengthText.style.color = "#fd7e14";
                    break;

                case 3:
                    strengthFill.style.width = "60%";
                    strengthFill.style.background = "#ffc107";
                    strengthText.textContent = "Aceptable";
                    strengthText.style.color = "#ffc107";
                    break;

                case 4:
                    strengthFill.style.width = "80%";
                    strengthFill.style.background = "#20c997";
                    strengthText.textContent = "Fuerte";
                    strengthText.style.color = "#20c997";
                    break;

                case 5:
                    strengthFill.style.width = "100%";
                    strengthFill.style.background = "#198754";
                    strengthText.textContent = "Muy segura";
                    strengthText.style.color = "#198754";
                    break;
            }

            validateMatch();
        }

        function updateRequirement(element, valid) {

            if (valid) {

                element.classList.add('valid');
                element.innerHTML = '✓ ' + element.textContent.substring(2);

            } else {

                element.classList.remove('valid');
                element.innerHTML = '✗ ' + element.textContent.substring(2);
            }
        }

        function validateMatch() {

            if (confirmPassword.value === '') {

                matchMessage.className = 'match-message';
                matchMessage.innerHTML = '';
                return;
            }

            if (password.value === confirmPassword.value) {

                matchMessage.className =
                    'match-message match-success';

                matchMessage.innerHTML = `
                    <i class="fas fa-circle-check"></i>

                    <div class="match-content">
                        <div class="match-title">
                            ¡Las contraseñas coinciden!
                        </div>

                        <div class="match-subtitle">
                            Ambas contraseñas son iguales.
                        </div>
                    </div>
                `;

            } else {

                matchMessage.className =
                    'match-message match-error';

                matchMessage.innerHTML = `
                    <i class="fas fa-circle-xmark"></i>

                    <div class="match-content">
                        <div class="match-title">
                            Las contraseñas no coinciden
                        </div>

                        <div class="match-subtitle">
                            Verifica que ambas contraseñas sean iguales.
                        </div>
                    </div>
                `;
            }
        }

        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validateMatch);

        form.addEventListener('submit', function(e) {

            const securePassword =
                password.value.length >= 8 &&
                /[A-Z]/.test(password.value) &&
                /[a-z]/.test(password.value) &&
                /[0-9]/.test(password.value) &&
                /[^A-Za-z0-9]/.test(password.value);

            if (!securePassword) {

                e.preventDefault();

                alert(
                    'La contraseña debe cumplir todos los requisitos de seguridad.'
                );

                return;
            }

            if (password.value !== confirmPassword.value) {

                e.preventDefault();

                alert(
                    'Las contraseñas no coinciden.'
                );
            }
        });
    </script>
    <script>
        const strengthFill = document.getElementById('strengthFill');
const strengthText = document.getElementById('strengthText');
    </script>

</body>

</html>

<?php

} else {

    echo '<script>
            alert("El enlace de recuperación es inválido o ha expirado.");
            window.location="../index.php";
        </script>';
}

$stmt->close();
mysqli_close($conexion);
?>