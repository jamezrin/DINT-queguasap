<?php

error_reporting(E_ALL);
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');

$config = parse_ini_file("config.ini");

include_once 'model/bd.inc.php';

include_once 'view/header.inc.php';
include_once 'view/footer.inc.php';

include_once 'controller/controller.inc.php';

include_once 'view/show_view.php';

actualizar_sesion();
handle_main();


