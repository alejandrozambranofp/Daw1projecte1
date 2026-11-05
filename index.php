<?php

// Cargar parámetros globales y base de datos
include_once "config/parameters.php";
include_once "config/dataBase.php";
include_once "models/Receta.php";
include_once "models/Bebida.php";

$controller_default = "productoController";

if (isset($_GET['controller'])) {
    $nombre_controlador = $_GET['controller'] . 'Controller';
} else {
    $nombre_controlador = $controller_default;
}

$file_controller = 'controllers/' . $nombre_controlador . '.php';
if (file_exists($file_controller)) {
    include_once $file_controller;
}

if (class_exists($nombre_controlador)) {
    $controlador = new $nombre_controlador();
    
    if (isset($_GET['action']) && method_exists($controlador, $_GET['action'])) {
        $action = $_GET['action'];
        $controlador->$action();
    } elseif (!isset($_GET['action'])) {
        $action = "index";
        $controlador->$action();
    } else {
        echo "La página buscada no existe";
    }
} else {
    echo "La página buscada no existe";
}