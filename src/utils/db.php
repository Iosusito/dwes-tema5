<?php

function nombreUsuarioRepetido($nombre): bool|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    $resultado = $mysqli->query(
        "select nombre from usuario where nombre=\"$nombre\""
    );

    if ($resultado === false) {
        return null;
    } else if ($resultado->num_rows == 1) {
        $resultado->free();
        $mysqli->close();
        return true;
    }

    $resultado->free();
    $mysqli->close();
    return false;
}

function registrarUsuario($nombre, $clave): bool|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare(
        "insert into usuario (nombre, clave) values (?, ?)"
    );
    if (!$sentencia) {
        $mysqli->close();
        return null;
    }

    // 2. Vinculación (bind)
    $clave = password_hash($clave, PASSWORD_BCRYPT);
    $vinculacion = $sentencia->bind_param(
        "ss",
        $nombre,
        $clave
    );
    if (!$vinculacion) {
        $sentencia->close();
        $mysqli->close();
        return null;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();

    $sentencia->close();
    $mysqli->close();

    return $resultado;
}

function iniciarSesion($nombre, $clave): bool|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    $resultado = $mysqli->query(
        "select nombre, clave from usuario where nombre=\"$nombre\""
    );
    if ($resultado === false) {
        return null;
    } else if ($resultado->num_rows != 1) {
        $resultado->free();
        $mysqli->close();
        return false;
    } else if (($fila = $resultado->fetch_assoc()) === null) {
        $resultado->free();
        $mysqli->close();
        return false;
    }

    $userVerified = password_verify($clave, $fila['clave']);

    $resultado->free();
    $mysqli->close();
    return $userVerified;
}

function addImageToDataBase($nombre, $ruta, $autor): bool|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    // 1. Preparación

    // Conseguimos el identificador del usuario (el autor)
    $resultado = $mysqli->query(
        "select id from usuario where nombre=\"$autor\""
    );
    if ($resultado === false) {
        return null;
    } else if ($resultado->num_rows == 1) {
        if (($fila = $resultado->fetch_assoc()) !== null) {
            $autor = $fila['id'];
        } else {
            return null;
        }
    } else {
        return null;
    }

    $sentencia = $mysqli->prepare(
        "insert into imagen (nombre, ruta, usuario) values (?, ?, ?)"
    );
    if (!$sentencia) {
        $mysqli->close();
        return null;
    }

    // 2. Vinculación (bind)
    $vinculacion = $sentencia->bind_param(
        "ssi",
        $nombre,
        $ruta,
        $autor
    );
    if (!$vinculacion) {
        $sentencia->close();
        $mysqli->close();
        return null;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();

    $sentencia->close();
    $mysqli->close();

    return $resultado;
}

function existeImagen(int $id): bool|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    $resultado = $mysqli->query(
        "select * from imagen where id=$id"
    );
    if ($resultado === false) {
        return null;
    } else if ($resultado->num_rows == 1) {
        $resultado->free();
        $mysqli->close();
        return true;
    }

    $resultado->free();
    $mysqli->close();
    return false;
}

function getRutaImagenPorId(int $id): string|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    $resultado = $mysqli->query(
        "select ruta from imagen where id=$id"
    );
    if ($resultado === false) {
        return null;
    }

    if (($fila = $resultado->fetch_assoc()) !== null) {
        $ruta = $fila['ruta'];
    }

    $resultado->free();
    $mysqli->close();
    return isset($ruta) ? $ruta : null;
}

function getImagenesPorNombre(string $nombre): array|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    $resultado = $mysqli->query(
        "select i.id as id,
        i.ruta as ruta,
        i.nombre as nombre,
        u.nombre as usuario
        from imagen i join usuario u
        on i.usuario = u.id
        where i.nombre like '%$nombre%'"
    );
    if ($resultado === false) {
        return null;
    } else {
        $imagenes = [];
        while (($fila = $resultado->fetch_assoc()) !== null) {
            $imagenInfo = [
                'id' => $fila['id'],
                'ruta' => $fila['ruta'],
                'nombre' => $fila['nombre'],
                'usuario' => $fila['usuario']
            ];
            array_push($imagenes, $imagenInfo);
        }
    }

    $resultado->free();
    $mysqli->close();
    return isset($imagenes) ? $imagenes : null;
}

function deleteImageFromDataBase(int $id): bool|null
{
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        return null;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare(
        "delete from imagen where id=?"
    );
    if (!$sentencia) {
        return null;
    }

    // 2. Vinculación (bind)
    $vinculacion = $sentencia->bind_param(
        "i",
        $id
    );
    if (!$vinculacion) {
        return null;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();

    $sentencia->close();
    $mysqli->close();

    return $resultado;
}
