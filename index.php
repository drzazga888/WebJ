<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 01:00
 */

include_once "Router.php";
include_once "Loader.php";
include_once "Template.php";

new Loader();

$controller = Router::getController();
$action = Router::getAction();

$controller = new $controller;
$controller->$action();