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
    return true;
}

/*
*	Comprueba el máximo número de caracteres del texto del estado del 
* 	usurario, configurable 
*	E:
*	S: booleano: número correcto
*	SQL: 
*/
function maximo_caracteres_estado()
{
    return true;
}

/*
*	Guarda el color seleccionado en el fichero de configuración
*	E: nada
*	S: boolean: si se ha podido guardar el color o no
*	SQL: nada
*/
function color_seleccionado()
{
    return true;
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
    return true;
}

/*
*	Función que da de alta un usuario
*	E:
*	S: int: codigo de error
*	SQL: INSERT INTO usuarios VALUES ($telefono, $contraseña, $nombre, $nombre_imagen)
*/
function alta_usuario_ok($telefono, $contrasena, $nombre, $nombre_imagen) {
    $conn = connection();
    $conectado = 0;
    $color_fondo = "#202020";
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

        return password_verify($contrasena, $hash_contrasena);;
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
