<?php

/*
*	Muestra el contenido de la parte central de la página
*	E:
*	S:
*	SQL:
*/
function show_content()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {    // GET
        if (!isset($_GET['cmd'])) {                // carga inicial de la página
            show_loging();
        } else {
            if ($_GET["cmd"] === "start"
                || $_GET["cmd"] === "logout") {
                switch ($_GET['cmd']) {
                    case 'start':
                        show_loging();
                        break;

                    case 'logout':
                        show_loging();
                        show_msg("Ha cerrado la sesión");
                        break;

                    default:
                        "error de conexión";
                        break;
                }
            } elseif (isset ($_SESSION["user"])) {
                switch ($_GET['cmd']) {
                    case 'chat':
                        show_chats();
                        break;

                    case 'nuevo_chat':
                        show_nuevo_chat();
                        break;

                    case 'ver_chat':
                        show_contacto_chat();
                        break;

                    case 'perfil':
                        show_perfil();
                        break;

                    case 'ajustes':
                        show_ajustes();
                        break;

                    case 'borrar_chat':
                        show_borrar_chat();
                        break;

                    default:
                        "error de conexión";
                        break;
                }
            } else {
                show_loging();
            }


        }
    } else {                                        // POST
        if (isset($_POST['login'])) {

            if (login_ok()) {

                show_chats();
            } else {
                show_msg("Error no enviado");
            }

        }

        if (isset($_POST['contestar'])) {

            if (tamaño_img()) {

                if (guardar_mensaje()) {
                    show_msg("Mensaje enviado");
                    show_chats();
                } else {
                    show_msg("Error no enviado");
                }

            } else {
                show_msg("Error imagen demasiado grande 20mb como maximo");
            }
        }

        if (isset($_POST['editar'])) {

            if (maximo_caracteres_estado()) {

                if (editar_perfil()) {
                    show_msg("Perfil editado");
                    show_chats();
                } else {
                    show_msg("Error no editado");
                }

            } else {
                show_msg("Error máximo de caracteres");
            }


        }

        if (isset($_POST['guardar_color'])) {

            if (color_seleccionado()) {
                show_msg("Color cambiado");
                show_chats();
            } else {
                show_msg("Error no se cambio de color");
            }
        }

        if (isset($_POST['backup'])) {

            if (backup_chat()) {
                show_msg("backup guardado");
                show_chats();
            } else {
                show_msg("Error no realizar el backup");
            }
        }

    }
}

/*
* Realiza algunas acciones según esté la sesión abierta o no
* E: 
* S: 
* SQL:
*/
function actualizar_sesion()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['login'])) {

            if (login_ok()) {
                $_SESSION['user'] = $_POST['numero'];
            }

        }

    } else {

        if (isset($_GET['cmd'])) {

            if ($_GET['cmd'] == 'logout') {

                unset($_SESSION);
                session_destroy();
            }

        }
    }
}


?>
