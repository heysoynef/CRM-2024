<?php
include 'db.php';

// Verificar si hay una notificación almacenada en la variable de sesión
if (isset($_SESSION['notification'])) {
    $notificationColor = "alert-success";
    // Verificar el contenido de la notificación y actualizar el color si corresponde
    if (
        $_SESSION['notification'] === "Por favor, completa todos los campos del formulario." ||
        $_SESSION['notification'] === "Las contraseñas no coinciden." ||
        $_SESSION['notification'] === "El campo 'Nombre' solo permite letras y espacios." ||
        $_SESSION['notification'] === "El campo 'Email' no tiene un formato válido." ||
        $_SESSION['notification'] === "El campo 'Password' debe tener al menos 8 caracteres."
    ) {
        $notificationColor = "alert-danger";
    }

    // Mostrar la notificación
    // Mostrar la notificación con el color correspondiente
    echo '<div id="notification" class="alert ' . $notificationColor . ' fixed-top text-center">' . $_SESSION['notification'] . '</div>';

    // Eliminar la notificación de la variable de sesión después de 3 segundos
    echo '<script>
        setTimeout(function() {
            document.getElementById("notification").remove();
        }, 3000);
    </script>';

    // Eliminar la notificación de la variable de sesión
    unset($_SESSION['notification']);
}

if (isset($_POST['registrar'])) {
    // Validar los datos (puedes agregar más validaciones según tus necesidades)
    if (empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["confirm_password"]) || empty($_POST["type"])) {
        $_SESSION['notification'] = "Por favor, completa todos los campos del formulario.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif ($_POST["password"] !== $_POST["confirm_password"]) {
        $_SESSION['notification'] = "Las contraseñas no coinciden.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["name"])) {
        $_SESSION['notification'] = "El campo 'Nombre' solo permite letras y espacios.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['notification'] = "El campo 'Email' no tiene un formato válido.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (strlen($_POST["password"]) < 8) {
        $_SESSION['notification'] = "El campo 'Password' debe tener al menos 8 caracteres.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Obtener los datos del formulario y aplicar medidas de seguridad
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        $confirmPassword = htmlspecialchars($_POST["confirm_password"]);
        $type = htmlspecialchars($_POST["type"]);

        // Verificar si el usuario ya está registrado
        $email = mysqli_real_escape_string($conn, $email);
        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $_SESSION['notification'] = "Ya existe un usuario registrado con el mismo email.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Aplicar hash a la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Solo si se necesita

            // Insertar los datos en la base de datos
            $sql = "INSERT INTO users (name, email, password, type) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $type);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['notification'] = "Registro exitoso.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $_SESSION['notification'] = "Error al registrar el usuario: " . mysqli_error($conn);
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
}

if (isset($_POST['actualizar'])) {
    // Validar los datos (puedes agregar más validaciones según tus necesidades)
    if (empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["type"])) {
        $_SESSION['notification'] = "Por favor, completa todos los campos del formulario.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $_POST["name"])) {
        $_SESSION['notification'] = "El campo 'Nombre' solo permite letras y espacios.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['notification'] = "El campo 'Email' no tiene un formato válido.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (strlen($_POST["password"]) < 8) {
        $_SESSION['notification'] = "El campo 'Password' debe tener al menos 8 caracteres.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Obtener los datos del formulario y aplicar medidas de seguridad
        $id = $_POST["id"];
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $password = htmlspecialchars($_POST["password"]);
        $type = htmlspecialchars($_POST["type"]);

        // Actualizar los datos del usuario
        $sql_update = "UPDATE users SET name = ?, email = ?, password = ?, type = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "ssssi", $name, $email, $password, $type, $id);

        if (mysqli_stmt_execute($stmt_update)) {
            // Actualización exitosa
            $_SESSION['notification'] = "Datos actualizados correctamente.";
            // Redireccionar a la página actual para mostrar la notificación
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Error al actualizar los datos
            $_SESSION['notification'] = "Error al actualizar los datos del usuario: " . mysqli_error($conn);
            // Redireccionar a la página actual para mostrar la notificación
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// *** BORRAR USUARIO ***
// Verifica si se ha enviado el formulario para eliminar el registro
if (isset($_POST['borrar'])) {
    // Obtén el ID del registro a eliminar
    $id = $_POST['id'];

    // Prepara la consulta SQL para eliminar el registro
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Ejecuta la consulta preparada y verifica si se completó correctamente
    if (mysqli_stmt_execute($stmt)) {
        // Guardar notificación en la variable de sesión
        $_SESSION['notification'] = "Registro eliminado exitosamente.";
    } else {
        // Guardar notificación de error en la variable de sesión
        $_SESSION['notification'] = "Error al eliminar el registro: " . mysqli_stmt_error($stmt);
    }

    // Cierra la consulta preparada
    mysqli_stmt_close($stmt);

    // Redireccionar a la página actual para mostrar la notificación
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
