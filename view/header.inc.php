<?php

/*
*	Muestra el encabezado
*	E:
*	S:
*	SQL:
*/
function show_header() {
    global $config;
    $nombreApp = $config["NOMBRE_HEADER"];
    $telefono = $_SESSION['telefono'];
    $info = consultar_usuario($telefono);

    $color_fondo = $info['color_fondo'];
    echo '<!DOCTYPE html>
			<html>
			<head>
				<title>' . $nombreApp . '</title>

				<link rel="icon" href="view/images/bd.jpg">
				<link rel="stylesheet" type="text/css" href="view/css/estilo.css">
				<meta charset="utf-8">
			</head>
			<body style="background-color:' . $color_fondo . ';">
				<div id="principal">';
}


?>
