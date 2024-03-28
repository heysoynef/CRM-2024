<?php
include '../db.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <!-- Kumbh Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kumbh Sans', sans-serif;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php
    // Verificar si se envió el formulario de registro
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Obtener los datos del formulario y aplicar medidas de seguridad
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        $confirmPassword = htmlspecialchars($_POST["confirm_password"]);
        $type = "CLIENT";

        // Validar los datos (puedes agregar más validaciones según tus necesidades)
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            echo '<div style="text-align: center; margin-top: 20px;">Por favor, completa todos los campos del formulario.
            <br><br><a href="../register.php">Regresar</a></div>';
        } elseif ($password !== $confirmPassword) {
            echo '<div style="text-align: center; margin-top: 20px;">Las contraseñas no coinciden.
            <br><br><a href="../register.php">Regresar</a></div>';
        } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            echo '<div style="text-align: center; margin-top: 20px;">El campo \'Nombre\' solo permite letras y espacios.
            <br><br><a href="../register.php">Regresar</a></div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo '<div style="text-align: center; margin-top: 20px;">El campo \'Email\' no tiene un formato válido.
            <br><br><a href="../register.php">Regresar</a></div>';
        } elseif (strlen($password) < 8) {
            echo '<div style="text-align: center; margin-top: 20px;">El campo \'Password\' debe tener al menos 8 caracteres.
            <br><br><a href="../register.php">Regresar</a></div>';
        } else {
            // Verificar si el usuario ya está registrado
            $email = mysqli_real_escape_string($conn, $email);
            $sql_check = "SELECT id FROM users WHERE email = '$email'";
            $result_check = mysqli_query($conn, $sql_check);

            if (mysqli_num_rows($result_check) > 0) {
                echo '<div style="text-align: center; margin-top: 20px;">Ya existe un usuario registrado con el mismo email. <a href="../register.php">Regresar</a></div>';
            } else {
                // Aplicar hash a la contraseña
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insertar los datos en la base de datos
                $name = mysqli_real_escape_string($conn, $name);
                $hashedPassword = mysqli_real_escape_string($conn, $hashedPassword);
                $sql = "INSERT INTO users (name, email, password, type) VALUES ('$name', '$email', '$password', '$type')";

                if (mysqli_query($conn, $sql)) {
                    echo '<div style="text-align: center; margin-top: 20px;">
                        <p>Registro exitoso. ¡Gracias por registrarte!</p>
                        <p><a href="../index.php">Iniciar sesión</a></p>
                      </div>';
                } else {
                    echo '<div style="text-align: center; margin-top: 20px;">
                        <p>Error al registrar el usuario: ' . mysqli_error($conn) . '</p>
                      </div>';
                }
            }
        }
    }
    ?>
</body>

</html>