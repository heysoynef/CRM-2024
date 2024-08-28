<?php
// Inicia sesión y verifica si el usuario ha iniciado sesión
function startSessionAndCheckLogin() {
    session_start(); // Iniciar sesión

    // Verificar si el usuario no ha iniciado sesión
    if (!isset($_SESSION["id"])) {
        // Redireccionar al formulario de inicio de sesión
        header("Location: index.php");
        exit();
    }
}

// Obtiene las tablas de la base de datos
function getDatabaseTables($conn) {
    $tablesQuery = "SHOW TABLES";
    $result = mysqli_query($conn, $tablesQuery);
    $tables = [];
    while ($row = mysqli_fetch_row($result)) {
        // Si el nombre de la tabla no es 'users', agregarlo al array de tablas
        if ($row[0] !== 'users') {
            $tables[] = $row[0];
        }
    }
    return $tables;
}

// Registra un nuevo usuario
function registerUser($conn, $name, $email, $password, $type, $client_id = null) {
    // Prepara la consulta para insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO users (name, email, password, type, cliente) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    // Verifica si la consulta se preparó correctamente
    if ($stmt) {
        // Vincula los parámetros y ejecuta la consulta
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password, $type, $client_id);
        mysqli_stmt_execute($stmt);

        // Cierra la consulta preparada
        mysqli_stmt_close($stmt);
    } else {
        echo 'Error en la consulta preparada: ' . mysqli_error($conn);
    }
}

// Obtiene la lista de usuarios de la base de datos
function getUsers($conn) {
    $sql = "SELECT id, name, email, password, type, cliente FROM users";
    $stmt = mysqli_prepare($conn, $sql);
    $users = [];

    // Verificar si la consulta se preparó correctamente
    if ($stmt) {
        // Ejecutar la consulta
        mysqli_stmt_execute($stmt);

        // Vincular variables de resultado
        mysqli_stmt_bind_result($stmt, $id, $name, $email, $password, $type, $cliente);

        // Obtener los resultados
        while (mysqli_stmt_fetch($stmt)) {
            $users[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'type' => $type,
                'cliente' => $cliente
            ];
        }

        // Liberar el resultado y cerrar la consulta
        mysqli_stmt_free_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo 'Error en la consulta preparada: ' . mysqli_error($conn);
    }

    return $users;
}

// Borra un usuario
function deleteUser($conn, $id) {
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo 'Error en la consulta preparada: ' . mysqli_error($conn);
    }
}

// Actualiza un usuario
function updateUser($conn, $id, $name, $email, $password, $type, $client_id = null) {
    $sql = "UPDATE users SET name = ?, email = ?, password = ?, type = ?, cliente = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $password, $type, $client_id, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo 'Error en la consulta preparada: ' . mysqli_error($conn);
    }
}
?>
