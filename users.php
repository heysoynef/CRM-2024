<?php
// Incluir el archivo de funciones
include 'db.php'; // Incluye tu archivo de conexión a la base de datos
include 'Functions/usersF.php'; 
include 'logic/UsersF.php';
// Iniciar sesión y verificar si el usuario ha iniciado sesión
startSessionAndCheckLogin();
include 'layouts/header.php';


// Obtener las tablas de la base de datos
$tables = getDatabaseTables($conn);
$tablesJson = json_encode($tables);

// Verificar si se ha enviado el formulario de registro
if (isset($_POST["registrar"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $type = $_POST["type"];
    $client_id = $type === 'Cliente' ? $_POST['client_id'] : null;

    registerUser($conn, $name, $email, $password, $type, $client_id);
}

// Verificar si se ha enviado el formulario de eliminación
if (isset($_POST["borrar"])) {
    $id = $_POST["id"];
    deleteUser($conn, $id);
}

// Verificar si se ha enviado el formulario de actualización
if (isset($_POST["actualizar"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $type = $_POST["type"];
    $client_id = $type === 'Cliente' ? $_POST['client_id'] : null;

    updateUser($conn, $id, $name, $email, $password, $type, $client_id);
}

// Obtener la lista de usuarios
$users = getUsers($conn);
?>

<!-- Resto del código HTML -->

<div class="container">
    <div class="row">
        <div class="col">
            <h3 class="mt-3">Usuarios</h3>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                Nuevo
            </button>
        </div>
        <div class="col">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h3 class="mt-3">Cliente</h3>
                        <!-- Botón para abrir el modal de agregar usuario tipo cliente -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalCliente">
                            Nuevo 
                        </button>
                    </div>
                    <div class="col">

                    </div>
                </div>
            </div>

            <!-- Modal para agregar usuario tipo cliente -->
            <div class="modal fade" id="myModalCliente" tabindex="-1" role="dialog"
                aria-labelledby="myModalClienteTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalClienteTitle">Agregar Cliente</h5>
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
                                    <input type="text" class="form-control" id="confirm_password"
                                        name="confirm_password">
                                </div>
                                <!-- Campo oculto para indicar el tipo de usuario -->
                                <input type="hidden" name="type" value="Cliente">
                                <div class="form-group" id="tableSelect" >
                                    <label for="client_id">Selecciona El Cliente Registrado:</label>
                                    <select class="form-control" name="client_id" id="client_id">
                                        <option value="" disabled selected>Selecciona</option>
                                        <!-- Las opciones se llenarán con JavaScript -->
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" name="registrar">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                        <th>Cliente</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener los registros de la base de datos
                    $sql = "SELECT id, name, email, password, type, cliente FROM users";
                    $stmt = mysqli_prepare($conn, $sql);

                    // Verificar si la consulta se preparó correctamente
                    if ($stmt) {
                        // Ejecutar la consulta
                        mysqli_stmt_execute($stmt);

                        // Vincular variables de resultado
                        mysqli_stmt_bind_result($stmt, $id, $name, $email, $password, $type, $cliente);

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
                                        <?php echo $cliente; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary editar" data-id="<?php echo $id; ?>"
                                                data-name="<?php echo $name; ?>" data-email="<?php echo $email; ?>"
                                                data-password="<?php echo $password; ?>" data-type="<?php echo $type; ?>"
                                                data-cliente="<?php echo $cliente; ?>" data-toggle="modal"
                                                data-target="#myModalEditar">Editar</button>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="submit" name="borrar" value="Borrar" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Estás seguro de que deseas borrar este elemento?')">
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            } while (mysqli_stmt_fetch($stmt));
                        } else {
                            ?>
                            <tr>
                                <td colspan="8">No hay registros disponibles</td>
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
                            <option value="Super_Admin">Super Administrador</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="registrar">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar -->
<div class="modal fade" id="myModalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalTitle"
    aria-hidden="true">
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
                        </select>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
    crossorigin="anonymous"></script>
<!-- JS Sticky Nav -->
<script src="js/jquery.sticky.js" async></script>
<script src="js/nav.js" async></script>

<!-- *** JS Custom *** -->

<script>
        // Escucha el evento clic del botón de editar
        $(document).on('click', '.editar', function () {
        // Obtén los datos del registro seleccionado
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var password = $(this).data('password');
        var type = $(this).data('type');
        var cliente = $(this).data('cliente');

        // Asigna los valores al formulario de edición en el modal
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_password').val(password);
        $('#edit_type').val(type);
        $('#client_id').val(cliente);

        // Abre el modal de edición
        $('#myModalEditar').modal('show');
    });

    $(document).ready(function () {
        // Convertir los nombres de las tablas de PHP a una variable de JavaScript
        var tables = <?php echo $tablesJson; ?>;

        // Función para actualizar las opciones del selector de tablas para el formulario de registro
        function updateTableOptionsForRegister() {
            $('#client_id').empty().append('<option value="" disabled selected>Selecciona</option>');
            tables.forEach(function (table) {
                $('#client_id').append($('<option></option>').attr('value', table).text(table));
            });
        }

        // Función para actualizar las opciones del selector de tablas para el formulario de edición
        function updateTableOptionsForEdit() {
            $('#client_id').empty().append('<option value="" disabled selected>Selecciona</option>');
            tables.forEach(function (table) {
                $('#client_id').append($('<option></option>').attr('value', table).text(table));
            });
        }

        // Mostrar u ocultar el selector de tablas basado en el tipo de usuario seleccionado
        $('#type').change(function () {
            if ($(this).val() === 'Cliente') {
                $('#tableSelect').show(); // Asegúrate de que este es el contenedor correcto para el formulario de registro
                updateTableOptionsForRegister();
            } else {
                $('#tableSelect').hide(); // Asegúrate de que este es el contenedor correcto para el formulario de registro
            }
        });

        $('#edit_type').change(function () {
            if ($(this).val() === 'Cliente') {
                $('#edit_tableSelect').show(); // Asegúrate de que este es el contenedor correcto para el formulario de edición
                updateTableOptionsForEdit();
            } else {
                $('#edit_tableSelect').hide(); // Asegúrate de que este es el contenedor correcto para el formulario de edición
            }
        });

        // Llamada inicial para asegurarse de que las opciones están actualizadas al cargar la página
        updateTableOptionsForRegister();
        updateTableOptionsForEdit();
    });

</script>

</body>

</html>