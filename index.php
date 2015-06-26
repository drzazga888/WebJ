<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 01:00
 */

session_start();

include_once "exceptions/FileNotFoundException.php";
include_once "exceptions/ClassNotFoundException.php";
include_once "exceptions/MethodNotFoundException.php";
include_once "Router.php";
include_once "Loader.php";
include_once "Template.php";

new Loader();

$controller = Router::getController();
$action = Router::getAction();
$params = Router::getParams();

try {
    if (!class_exists($controller))
        throw new ClassNotFoundException();
    $controller = new $controller;
    if (!method_exists($controller, $action))
        throw new MethodNotFoundException();
    $controller->$action($params);
} catch ( FileNotFoundException $e ) {
    Router::error404();
    die();
} catch ( ClassNotFoundException $e ) {
    Router::error404();
    die();
} catch ( MethodNotFoundException $e ) {
    Router::error404();
    die();
}

unset($_SESSION["message"]);
unset($_SESSION["message-class"]);