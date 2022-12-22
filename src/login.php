<?php

/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();

if (isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

require 'utils/db.php';

if ($_POST) {
    if (isset($_POST['nombre'])) {
        $nombre = htmlspecialchars(trim($_POST['nombre']));

        $nombreVacio = mb_strlen($nombre) > 0 ? false : true;

        $nombreValido = !$nombreVacio;
    } else {
        $nombreVacio = true;
        $nombreValido = false;
    }

    if (isset($_POST['clave'])) {
        $clave = htmlspecialchars(trim($_POST['clave']));

        $claveVacia = mb_strlen($clave) > 0 ? false : true;

        $claveValida = !$claveVacia;
    } else {
        $claveVacia = true;
        $claveValida = false;
    }

    $todoValido = $nombreValido && $claveValida ? true : false;

    if ($todoValido) {
        $loginOk = iniciarSesion($nombre, $clave);

        if ($loginOk) {
            $_SESSION['usuario'] = $nombre;
            //redigir al inicio una vez ya logeado
            header('location: index.php');
            exit();
        } else if ($loginOk === null) {
            $dberror = true;
        }
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú.
 * - TODO: formulario con nombre de usuario y contraseña.
 */
?>

<h1>Iniciar sesión</h1>

<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="filter.php">Filtrar imágenes</a></li>
    <li><strong>Inicia sesion</strong></li>
    <li><a href="signup.php">Regístrate</a></li>
</ul>

<form action="login.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre" 
        value="<?php echo isset($nombre) ? $nombre : ""; ?>">
    </p>
    <?php
    if (isset($nombreVacio) && $nombreVacio) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    ?>
    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
    </p>
    <?php
    if (isset($claveVacia) && $claveVacia) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    if (isset($loginOk) && !$loginOk) {
        echo "<p>ERROR: Usuario y/o contraseña incorrrectas</p>";
    }
    ?>
    <input type="submit" value="Iniciar sesión">
    <?php // errores de la base de datos
    if (isset($dberror) && $dberror) {
        echo "<p>ERROR: Hemos tenido un problema interno, prueba más tarde</p>";
    }
    ?>
</form>