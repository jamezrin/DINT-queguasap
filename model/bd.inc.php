<?php

/*
*	Conexión a la base de datos
*	E:
*	S: conn (variable de tipo connection)
*	SQL: nada
*/
function connection()
{
    global $config;

    $conn = new mysqli(
        $config['DB_HOST'],
        $config['DB_USER'],
        $config['DB_PASSWORD'],
        $config['DB_NAME']
    );

    $conn->set_charset('utf8mb4');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    return $conn;
}

/*
*	Funcion que modifica el perfil
*	E:
*	S:
*	SQL: UPDATE into usuario ...
*/
function editar_estado($telefono, $nuevo_estado) {
    $conn = connection();
    try {
        $stmt = $conn->prepare("UPDATE usuarios SET estado = ? WHERE telefono = ?");
        $stmt->bind_param("ss", $nuevo_estado, $telefono);

        $stmt->execute();
        $stmt->close();
        return true;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

/*
*	Guarda el color seleccionado en el fichero de configuración
*	E: nada
*	S: boolean: si se ha podido guardar el color o no
*	SQL: nada
*/
function cambiar_color($telefono, $color) {
    $color_hex = mapear_color($color);
    $conn = connection();

    try {
        $stmt = $conn->prepare("UPDATE usuarios SET color_fondo = ? WHERE telefono = ?");
        $stmt->bind_param("ss",
            $color_hex,
            $telefono);

        $stmt->execute();
        $stmt->close();
        return true;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function enviar_mensaje($telefono_emisor, $telefono_contacto, $adjunto, $mensaje) {
    $conn = connection();

    try {
        $stmt = $conn->prepare("
            INSERT INTO envia_mensaje (emisor, receptor, texto, archivo) 
            VALUES (?, ?, ?, ?)");

        $stmt->bind_param("ssss",
            $telefono_emisor,
            $telefono_contacto,
            $mensaje,
            $adjunto);

        $stmt->execute();
        $stmt->close();
        return true;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

/*
*	Funcion que guarda el chat en un fichero backup.txt
*	E:
*	S: booleano: guardado correctamente
*	SQL: SELECT * FROM Chats WHERE ChatId = $chat_id
*/
function backup_chat($telefono, $telefono_contacto, $nombre_archivo) {
    if (trim($nombre_archivo) !== '') {
        $conn = connection();

        $stmt = $conn->prepare("
            SELECT momento, texto, emisor 
            FROM envia_mensaje 
            WHERE (envia_mensaje.emisor = ? AND envia_mensaje.receptor = ?) 
               OR (envia_mensaje.emisor = ? AND envia_mensaje.receptor = ?)
        ");

        $stmt->bind_param("ssss",
            $telefono, $telefono_contacto,
            $telefono_contacto, $telefono);
        $stmt->execute();
        $result = $stmt->get_result();

        header('Content-type:text/plain');
        header('Content-Disposition: attachment; filename ="'.$nombre_archivo.'.txt"');

        while ($row = $result->fetch_assoc()) {
            $fecha = $row['momento'];
            $texto = $row['texto'];
            $emisor = $row['emisor'];
            echo "[$fecha] $emisor: $texto\n";
        }

        $stmt->close();
        return true;
    } else {
        show_msg("El nombre del archivo no puede ser un espacio en blanco");
    }

    return false;
}

/*
*	Función que da de alta un usuario
*	E:
*	S: int: codigo de error
*	SQL: INSERT INTO usuarios VALUES ($telefono, $contraseña, $nombre, $nombre_imagen)
*/
function alta_usuario_ok($telefono, $contrasena, $nombre, $nombre_imagen) {
    global $config;
    $conn = connection();
    $conectado = 0;
    $color_fondo = $config['BACK_COLOR'];
    $estado = "Hola estoy usando queguasap";
    $hash_contrasena = password_hash($contrasena, PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO usuarios VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisss",
            $telefono,
            $nombre,
            $hash_contrasena,
            $conectado,
            $nombre_imagen,
            $color_fondo,
            $estado
        );

        $stmt->execute();
        $stmt->close();
        return 0;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function inicio_usuario_ok($telefono, $contrasena) {
    $conn = connection();

    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE telefono = ?");
        $stmt->bind_param("s", $telefono);

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_assoc();
        $hash_contrasena = $rows['contrasena'];
        $stmt->close();

        return password_verify($contrasena, $hash_contrasena);
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function cambiar_conectado($telefono, $conectado) {
    $conn = connection();

    try {
        $stmt = $conn->prepare("UPDATE usuarios SET conectado = ? WHERE telefono = ?");
        $stmt->bind_param("is",
            $conectado,
            $telefono);

        $stmt->execute();
        $stmt->close();
        return 0;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function editar_imagen($telefono, $imagen) {
    $conn = connection();

    try {
        $stmt = $conn->prepare("UPDATE usuarios SET imagen = ? WHERE telefono = ?");
        $stmt->bind_param("ss",
            $imagen,
            $telefono);

        $stmt->execute();
        $stmt->close();
        return 0;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function consultar_usuario($telefono) {
    $conn = connection();

    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE telefono = ?");
        $stmt->bind_param("s", $telefono);

        $stmt->execute();
        $result = $stmt->get_result();
        $resultado = $result->fetch_assoc();
        $stmt->close();
        return $resultado;
    } catch (Exception $e) {
        return $e->getCode();
    }
}

function consultar_chat($telefono, $telefono_contacto) {
    $conn = connection();

    $stmt = $conn->prepare("
        SELECT usuarios.nombre AS nombre_emisor, telefono, momento, texto, archivo 
        FROM envia_mensaje 
        INNER JOIN usuarios
            ON envia_mensaje.emisor = usuarios.telefono
        WHERE (emisor = ? AND receptor = ?) 
            OR (receptor = ? AND emisor = ?)
        ORDER BY momento DESC;
    ");

    $stmt->bind_param("ssss",
        $telefono, $telefono_contacto,
        $telefono, $telefono_contacto);

    $stmt->execute();

    $result = $stmt->get_result();

    $stmt->close();

    return $result;
}

function consultar_nuevos_contactos($telefono) {
    $conn = connection();

    $stmt = $conn->prepare("
        SELECT DISTINCT telefono, conectado, nombre 
        FROM usuarios
        WHERE telefono NOT IN (
            SELECT usuarios.telefono FROM (
                SELECT DISTINCT receptor AS telefono FROM envia_mensaje WHERE emisor = ?
                UNION
                SELECT DISTINCT emisor AS telefono FROM envia_mensaje WHERE receptor = ?
            ) conversacion INNER JOIN usuarios ON usuarios.telefono = conversacion.telefono
        );
    ");

    $stmt->bind_param("ss",
        $telefono,
        $telefono);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

function consultar_contactos_existentes($telefono) {
    $conn = connection();

    $stmt = $conn->prepare("
        SELECT usuarios.telefono, usuarios.conectado, usuarios.nombre FROM (
            SELECT DISTINCT receptor AS telefono FROM envia_mensaje WHERE emisor = ?
            UNION
            SELECT DISTINCT emisor AS telefono FROM envia_mensaje WHERE receptor = ?
        ) conversacion INNER JOIN usuarios ON usuarios.telefono = conversacion.telefono;
    ");

    $stmt->bind_param("ss",
        $telefono,
        $telefono);

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}