<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación</title>

    <!-- Kumbh Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kumbh Sans', sans-serif;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    include '../db.php';

    // Verificar si se envió el formulario de inicio de sesión
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Obtener los datos del formulario de inicio de sesión
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);

        // Validar los datos (puedes agregar más validaciones según tus necesidades)
        if (empty($email) || empty($password)) {
            echo "<div style='text-align: center; margin-top: 20px;'>Por favor, completa todos los campos del formulario de inicio de sesión. <a href='index.php'>Regresar</a></div>";
        } else {
            // Verificar las credenciales del usuario
            $stmt = $conn->prepare("SELECT id, name, email, type FROM users WHERE email = ? AND password = ?");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Inicio de sesión exitoso, almacenar información en la sesión
                $row = $result->fetch_assoc();
                $_SESSION["id"] = $row["id"];
                $_SESSION["name"] = $row["name"];
                $_SESSION["email"] = $row["email"];
                $_SESSION["type"] = $row["type"];
                $_SESSION["logged_in"] = true;

                // Redireccionar a calendar.php u otra página de tu elección
                header("Location: ../chart.php?type=" . $_SESSION['type']);
                exit();
            } else {
                echo "<div style='text-align: center; margin-top: 20px;'>Credenciales inválidas. Por favor, verifica tu email y contraseña. <a href='../index.php'>Regresar</a></div>";
            }

            // Cerrar conexión
            $stmt->close();
            $conn->close();
        }
    } else {
        echo "<div style='text-align: center; margin-top: 20px;'>Aquí no hay nada para ti. <a href='../index.php'>Regresar</a></div>";
    }
    ?>
</body>

</html>