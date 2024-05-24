<?php
// Obtener el nombre de la tabla de la URL
$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : "";
?>

<div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close mt-3">
            <span class="js-menu-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                    fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg></span>
        </div>
    </div>
    <div class="site-mobile-menu-body"></div>
</div>

<div class="top-bar">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <a href="#" class=""><span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                            <path
                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                        </svg></span> <span class="d-none d-md-inline-block">contacto@monkeysolutions.mx</span>
                </a>
                <span class="mx-md-2 d-inline-block"></span>
                <a href="#" class=""><span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                            <path
                                d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                        </svg></span> <span class="d-none d-md-inline-block">(55) 5113 4373</span>
                </a>

                <div class="float-right">
                    <a href="#" class=""><span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                                <path
                                    d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
                            </svg></span> <span class="d-none d-md-inline-block">Twitter</span>
                    </a>
                    <span class="mx-md-2 d-inline-block"></span>
                    <a href="#" class=""><span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                <path
                                    d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                            </svg></span> <span class="d-none d-md-inline-block">Facebook</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<header class="site-navbar js-sticky-header site-navbar-target" role="banner">

    <div class="container">
        <div class="row align-items-center position-relative">
            <div class="site-logo">
                <!-- <a href="#" onclick="redirectToIndex()">
                    <img class="img-fluid" width="25%" height="auto" src="imgs/logo.webp" alt="logo" style="cursor: pointer;">
                </a> -->
                <a href="#">
                    <img class="img-fluid" width="25%" height="auto" src="imgs/logo.webp" alt="logo">
                </a>
            </div>

            <!-- <script>
                function redirectToIndex() {
                    window.location.href = "index.php";
                }
            </script> -->

            <div class="col-12">
                <nav class="site-navigation text-right ml-auto " role="navigation">
                    <ul class="site-menu main-menu js-clone-nav ml-auto d-none d-lg-block">
                        <li id="opcion_mes"><a href="chart.php" class="nav-link">Graficos</a></li>
                        <?php
                        // Obtener el tipo de usuario de la sesión
                        $sessionType = isset($_SESSION["type"]) ? trim($_SESSION["type"]) : "";

                        // Verificar el tipo de usuario
                        if ($sessionType === "Cliente") {
                            ?>
                            <li id="option_users" style="display: none;"><a href="users.php" class="nav-link">Usuarios</a>
                            </li>
                            <li id="export"><a href="data.php?id=<?php echo urlencode($tabla); ?>"
                                    class="nav-link">Exportar</a></li>
                            <?php
                        } else {
                            ?>
                            <li id="option_users"><a href="users.php" class="nav-link">Usuarios</a></li>
                            <li id="option_client"><a href="clients.php" class="nav-link">Clientes</a></li>
                            <?php

                        }
                        ?>
                        <li><a href="logic/logout.php?logout=true" class="nav-link">Salir</a></li>
                    </ul>

                </nav>
            </div>

            <!-- VALIDACIÓN -->
            <script>
                function verificarPermisos() {
                    var sessionType = "<?php echo $sessionType; ?>";

                    if (sessionType === "Cliente") {
                        // Ocultar las opciones del menú "Clientes" y "Usuarios"
                        document.getElementById("option_client").style.display = "none";
                        document.getElementById("option_users").style.display = "none";

                        // Mostrar un mensaje de alerta
                        alert("No tienes permisos para realizar esta acción.");

                        // Prevenir el evento de eliminación
                        return false;
                    }

                    // Permitir el evento de eliminación para otros roles
                    return true;
                }
            </script>


            <div class="toggle-button d-inline-block d-lg-none"><a href="#"
                    class="site-menu-toggle py-5 js-menu-toggle text-black"><span class="h3"><svg
                            xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                            class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                        </svg></span></a>
            </div>
        </div>
    </div>
</header>

