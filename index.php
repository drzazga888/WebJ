<?php

// rozpoczęcie sesji
session_start();

// dołączanie niezbędnych plików
include_once "exceptions/FileNotFoundException.php";
include_once "exceptions/ClassNotFoundException.php";
include_once "exceptions/MethodNotFoundException.php";
include_once "Router.php";
include_once "Loader.php";
include_once "Template.php";

// rejestracji funkcji autołaujących klasy
new Loader();

// pobranie kontrolera, akcji i perametrów na podstawie adresu (metoda GET)
$controller = Router::getController();
$action = Router::getAction();
$params = Router::getParams();

// próba wywołania metody => gdy się nie uda to nastąpi wyświetlenie błedu HTTP 404 - Not Found
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

// usuwanie komunikatu - użytkownik już go zobaczył
unset($_SESSION["message"]);
unset($_SESSION["message-class"]);