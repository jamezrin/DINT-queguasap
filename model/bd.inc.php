<?php

/*
*	Conexión a la base de datos
*	E: nada (lee directamente)
*	S: conn (variable de tipo connection)
*	SQL: nada
*/
function connection()
{
    return true;
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
    unset($_SESSION['user']);
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
*	S: booleano: guardado correctamente
*	SQL: INSERT INTO Usuarios VALUES ($telefono, $contraseña, $imagenPerfil)
*/
function alta_usuario_ok()
{
    return true;
}

/*
*   Función que borra un chat de la base de datos
 *  E:
 *  S: boolean: si se ha podido borrar el chat o no
 *  SQL: DELETE FROM Chats WHERE ChatId = $chat_id
*/
function borrar_chat_ok()
{
    return true;
}

/*
 * Función que valida los datos al registrarse:
 * asdf
 * sdf
 * sdfsd
 *
 * E: nada (lee los datos directamente)
 * S: boolean: datos son validos
 * SQL:
 */
function validar_datos_registro()
{
    return true;
}

?>
