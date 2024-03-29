<?php
session_start(); // Iniciar sesión

// Verificar si el paciente no ha iniciado sesión
if (!isset($_SESSION["id"])) {
    // Redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

include 'logic/users.php';
include 'layouts/header.php';

// Suponiendo que $conn es tu conexión a la base de datos
$tablesQuery = "SHOW TABLES WHERE Tables_in_crm NOT LIKE 'users'";
$result = mysqli_query($conn, $tablesQuery);
$tables = [];
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}
// Convertir el array de tablas a JSON para usarlo en JavaScript
$tablesJson = json_encode($tables);

?>

<div class="container">
    <div class="row">
        <div class="col">
            <h3 class="mt-3">Usuarios</h3>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                Nuevo
            </button>
        </div>
        <div class="col">
        </div>
    </div>
</div>

<div class="container my-4">
    <h5>Lista de Registros</h5>
    <small>Aquí se listan los usuarios del sistema, puedes borrar, editar y crear.</small>
    <?php
    $sessionType = trim($_SESSION["type"]); // Eliminar espacios en blanco al inicio y final

    if ($sessionType !== "Super_Admin") {
    ?>
        <h4 class="my-3">Hey! No tienes permisos, fuera de aquí!</h4>
    <?php
    } else {
    ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Tipo</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener los registros de la base de datos
                    $sql = "SELECT id, name, email, password, type FROM users";
                    $stmt = mysqli_prepare($conn, $sql);

                    // Verificar si la consulta se preparó correctamente
                    if ($stmt) {
                        // Ejecutar la consulta
                        mysqli_stmt_execute($stmt);

                        // Vincular variables de resultado
                        mysqli_stmt_bind_result($stmt, $id, $name, $email, $password, $type);

                        // Mostrar los registros en la tabla
                        if (mysqli_stmt_fetch($stmt)) {
                            do {
                                // Mostrar los datos en la tabla
                    ?>
                                <tr>
                                    <td>
                                        <?php echo $id; ?>
                                    </td>
                                    <td>
                                        <?php echo $name; ?>
                                    </td>
                                    <td>
                                        <?php echo $email; ?>
                                    </td>
                                    <td>
                                        <?php echo $password; ?>
                                    </td>
                                    <td>
                                        <?php echo $type; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary editar" data-id="<?php echo $id; ?>" data-name="<?php echo $name; ?>" data-email="<?php echo $email; ?>" data-password="<?php echo $password; ?>" data-type="<?php echo $type; ?>" data-toggle="modal" data-target="#myModalEditar">Editar</button>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="submit" name="borrar" value="Borrar" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas borrar este elemento?')">
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            } while (mysqli_stmt_fetch($stmt));
                        } else {
                            ?>
                            <tr>
                                <td colspan="6">No hay registros disponibles</td>
                            </tr>
                    <?php
                        }

                        // Liberar el resultado
                        mysqli_stmt_free_result($stmt);
                        // Cerrar la consulta
                        mysqli_stmt_close($stmt);
                    } else {
                        echo 'Error en la consulta preparada: ' . mysqli_error($conn);
                    }

                    // Cerrar la conexión
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    <?php
    }
    ?>
</div>

<!-- Modal para agregar -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalTitle">Formulario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="text" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Password:</label>
                        <input type="text" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="form-group">
                        <label for="type">Selecciona el tipo de usuario:</label>
                        <select class="form-control" name="type" id="type">
                            <option value="" disabled selected>Selecciona</option>
                            <option value="SUPER-MNK">Super Administrador</option>
                            <option value="ADMIN-MNK">Administrador</option>
                            <option value="CLIENT-MNK">Cliente</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="registrar">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar -->
<div class="modal fade" id="myModalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalTitle">Editar Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label for="name">Nombre:</label>
                        <input type="text" class="form-control" id="edit_name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="text" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="type">Tipo de usuario:</label>
                        <select class="form-control" name="type" id="edit_type">
                            <option value="" disabled selected>Selecciona</option>
                            <option value="Super_Admin">Super Administrador</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Cliente">Cliente</option>
                        </select>
                        <div class="form-group" id="tableSelect" style="display: none;">
                            <label for="table_name">Selecciona El cliente registrado:</label>
                            <select class="form-control" name="table_name" id="table_name">
                                <option value="" disabled selected>Selecciona</option>
                                <!-- Las opciones se llenarán con JavaScript -->
                            </select>
                        </div>

                    </div>
                    <button type="submit" name="actualizar" class="btn btn-primary mt-3">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery 3.6.0 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4.6.2 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<!-- JS Sticky Nav -->
<script src="js/jquery.sticky.js" async></script>
<script src="js/nav.js" async></script>

<!-- *** JS Custom *** -->
<script>
    // Escucha el evento clic del botón de editar
    $(document).on('click', '.editar', function() {
        // Obtén los datos del registro seleccionado
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var password = $(this).data('password');
        var type = $(this).data('type');

        // Asigna los valores al formulario de edición en el modal
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_password').val(password);
        $('#edit_type').val(type);

        // Abre el modal de edición
        $('#myModalEditar').modal('show');
    });
</script>

</body>

</html>