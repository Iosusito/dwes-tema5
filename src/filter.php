<?php
/*********************************************************************************************************************
 * Este script muestra un formulario a través del cual se pueden buscar imágenes por el nombre y mostrarlas. Utiliza
 * el operador LIKE de SQL para buscar en el nombre de la imagen lo que llegue por $_GET['nombre'].
 * 
 * Evidentemente, tienes que controlar si viene o no por GET el valor a buscar. Si no viene nada, muestra el formulario
 * de búsqueda. Si viene en el GET el valor a buscar (en $_GET['nombre']) entonces hay que preparar y ejecutar una 
 * sentencia SQL.
 * 
 * El valor a buscar se tiene que mantener en el formulario.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();

require 'utils/db.php';

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

if ($_GET && isset($_GET['nombre'])) {
    $nombre = htmlspecialchars(trim($_GET['nombre']));

    if (mb_strlen($nombre) > 0) {
        $imagenes = getImagenesPorNombre($nombre);
    }
}
?>

<?php
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: completa el código de la vista añadiendo el menú de navegación.
 * - TODO: en el formulario falta añadir el nombre que se puso cuando se envió el formulario.
 * - TODO: debajo del formulario tienen que aparecer las imágenes que se han encontrado en la base de datos.
 */
?>
<h1>Galería de imágenes</h1>

<?php
if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><strong>Filtrar imágenes</strong></li>
            <li><a href="login.php">Inicia sesion</a></li>
            <li><a href="signup.php">Regístrate</a></li>
        </ul>
    END;
} else {
    echo <<<END
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="add.php">Añadir imagen</a></li>
            <li><strong>Filtrar imágenes</strong></li>
            <li><a href="logout.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
}
?>

<h2>Busca imágenes por filtro</h2>

<form method="get">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre" 
        value="<?php echo isset($nombre) ? $nombre : ""; ?>">
    </p>
    <p>
        <input type="submit" value="Buscar">
    </p>
</form>

<?php
if (isset($imagenes) && $imagenes != null) {
    if (sizeof($imagenes) == 0) {
        echo "<h2>No hay imágenes.</h2>";
    } else {
        echo "<h2>Imágenes encontradas: " . sizeof($imagenes) . "</h2>";
        for ($i = 0; $i < sizeof($imagenes); $i++) {
            $imagen = $imagenes[$i];
            echo <<<END
                <figure>
                    <div>{$imagen['nombre']} (subida por {$imagen['usuario']})</div>
                    <img src="{$imagen['ruta']}" width="200px">
                    <a href="delete.php?id={$imagen['id']}">Borrar</a>
                </figure>
            END;
        }
    }
}
?>