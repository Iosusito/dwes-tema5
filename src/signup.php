<?php
/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * - El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * - La contraseña tiene que ser, al menos, de 8 caracteres.
 * - Las contraseñas tiene que coincidir.
 * 
 * La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
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

        if (!$nombreVacio) {
            $alnum = ctype_alnum($nombre);

            if ($alnum) {
                $nombreRepetido = nombreUsuarioRepetido($nombre);

                $nombreValido = !$nombreRepetido;
            } else {
                $nombreValido = false;
            }
        } else {
            $nombreValido = false;
        }
    } else {
        $nombreVacio = true;
        $nombreValido = false;
    }

    if (isset($_POST['clave'])) {
        $clave = htmlspecialchars(trim($_POST['clave']));

        $claveVacia = mb_strlen($clave) > 0 ? false : true;
        
        if (!$claveVacia) {
            $claveMuyCorta = mb_strlen($clave) < 8 ? true : false;
            $claveValida = !$claveMuyCorta;
        } else {
            $claveValida = false;
        }
    } else {
        $claveVacia = true;
        $claveValida = false;
    }

    if (isset($_POST['repite_clave'])) {
        $repiteClave = htmlspecialchars(trim($_POST['repite_clave']));

        $repiteClaveVacia = mb_strlen($repiteClave) > 0 ? false : true;

        if (!$repiteClaveVacia) {
            $clavesCoinciden = $clave === $repiteClave;
            $repiteClaveValida = $clavesCoinciden;
        } else {
            $repiteClaveValida = false;
        }
    } else {
        $repiteClaveVacia = true;
        $repiteClaveValida = false;
    }

    $todoValido = $nombreValido && $claveValida && $repiteClaveValida ? true : false;

    if ($todoValido) {
        $signupOk = registrarUsuario($nombre, $clave);
        if ($signupOk) {
            //logearse automáticamente al registrarse
            $_SESSION['usuario'] = $nombre;
            //redigir al inicio una vez ya logeado
            header('location: index.php');
            exit();
        } else if ($signupOk == null) {
            $dbError = true;
        }
    }
}


/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: los errores que se produzcan tienen que aparecer debajo de los campos.
 * - TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */
?>
<h1>Regístrate</h1>

<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="filter.php">Filtrar imágenes</a></li>
    <li><a href="login.php">Iniciar sesión</a></li>
    <li><strong>Regístrate</strong></li>
</ul>

<form action="signup.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre" 
        value="<?php echo isset($nombre) ? $nombre : ""; ?>">
    </p>
    <?php // errores del nombre
    if (isset($nombreVacio) && $nombreVacio) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    if (isset($nombreRepetido) && $nombreRepetido) {
        echo "<p>ERROR: Este nombre de usuario ya está en uso</p>";
    }
    if (isset($alnum) && !$alnum) {
        echo "<p>ERROR: El nombre de usuario solo admite carácteres alfanuméricos</p>";
    }
    ?>
    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
    </p>
    <?php // errores de la contraseña
    if (isset($claveVacia) && $claveVacia) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    if (isset($claveMuyCorta) && $claveMuyCorta) {
        echo "<p>ERROR: La contraseña tiene que se de mínimo 8 carácteres</p>";
    }
    ?>
    <p>
        <label for="repite_clave">Repite la contraseña</label>
        <input type="password" name="repite_clave" id="repite_clave">
    </p>
    <?php // errores de la contraseña repetida
    if (isset($repiteClaveVacia) && $repiteClaveVacia) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    if (isset($clavesCoinciden) && !$clavesCoinciden) {
        echo "<p>ERROR: Las contraseñas no coinciden</p>";
    }
    ?>
    <p>
        <input type="submit" value="Regístrate">
    </p>
    <?php // errores de la base de datos
    if (isset($dbError) && $dbError) {
        echo "<p>ERROR: Hemos tenido un problema interno, prueba más tarde</p>";
    }
    ?>
</form>
