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
*	Comprueba login
*	E: nada (lee directamente)
*	S: booleano: conexión correcta
*	SQL: select * from usuarios WHERE ...
*/
function login_ok()
{
    return true;
}


/*
*	Función para terminar la sesión
*	E: nada
*	S: nada
*	SQL: nada
*/
function unset_session()
{
    unset($_SESSION['telefono']);
}

/*
*	Guardar el mensaje en la BD
*	E: nada (lee directamente)
*	S: boolean: operación correcta
*	SQL: INSERT into Mensaje (texto) values (?);	SELECT idMensaje, texto, fecha, hora, fichero, telefono from Mensajes
*/
function guardar_mensaje()
{
    return true;
}

/*
*	Funcion que modifica el perfil
*	E:
*	S:
*	SQL: UPDATE into usuario ...
*/
function editar_perfil()
{
    $conn = connection();
    $nuevo_estado = $_POST['nuevo_estado'];
    $telefono = $_SESSION['telefono'];
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
function color_seleccionado() {

    $color = $_POST['color'];
    $color_hex = mapear_color($color);
    $conn = connection();

    $telefono = $_SESSION['telefono'];
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

/*
*	Comprueba el tamaño de la imagen seleccionada, el tamaño de la 
* 	imagen estara en el fichero de configuración
*	E:
*	S: booleano: tamaño correcto
*	SQL: nada
*/
function tamaño_img()
{
    return true;
}


/*
*	Funcion que guarda el chat en un fichero backup.txt
*	E:
*	S: booleano: guardado correctamente
*	SQL: SELECT * FROM Chats WHERE ChatId = $chat_id
*/
function backup_chat()
{
    $nombre_archivo = $_POST['nombre'];
    $telefono = $_SESSION['telefono'];
    $telefono_contacto = $_POST['telefono_contacto'];

    if(trim($nombre_archivo) !== '') {
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

/*
*   Función que borra un chat de la base de datos
 *  E:
 *  S: boolean: si se ha podido borrar el chat o no
 *  SQL: DELETE FROM Chats WHERE ChatId = $chat_id
*/
function borrar_chat_ok() {
    return true;
}

function mapear_color($color) {
    global $config;
    switch ($color) {
        case "defecto":
            return $config['BACK_COLOR'];
        case "verde":
            return "#008f39";
        case "rojo":
            return "#cb3234";
        case "blanco":
            return "#ffffff";
        case "azul":
            return "#3b83bd";
        case "rosa":
            return "#ff0080";
        default:
            return "#000000";
    }
}