<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Monkey S.</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet" />
    <!-- Kumbh Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center mb-3">
                                        <img src="imgs/logo.webp" style="width: 185px;" alt="logo">
                                        <!-- <h4 class="mt-3 mb-3 pb-1">We are Monkey's Team</h4> -->
                                    </div>

                                    <form action="logic/register_add.php" method="POST" autocomplete="off">
                                        <p>Por favor, ingrese a sus datos</p>

                                        <div class="form-outline mb-4">
                                            <input type="text" name="name" id="name" class="form-control" placeholder="" autocomplete="off" value="" />
                                            <label class="form-label" for="name">Nombre Completo</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="email" name="email" id="email" class="form-control" placeholder="" autocomplete="off" value="" />
                                            <label class="form-label" for="email">Correo Electrónico</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="password" name="password" id="password" class="form-control" autocomplete="off" value="" />
                                            <label class="form-label" for="password">Contraseña</label>
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="off" value="" />
                                            <label class="form-label" for="confirm_password">Confirmar Contraseña</label>
                                        </div>

                                        <div class="text-center pt-1 mb-3 pb-1">
                                            <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Sign up</button>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿Ya tienes una cuenta?</p>
                                            <a href="index.php" class="btn btn-outline-danger">Iniciar Sesión</a>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">We are more than just a company</h4>
                                    <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                        exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.js"></script>
</body>

</html>