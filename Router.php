<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 00:55
 */

class Router {

    public static function error404() {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Strony nie znaleziono");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $error404 = new Template("error404");
        $baseBottom = new Template("base_bottom");
        $baseBottom->loadScript("scripts");

        // rendering
        $baseTop->render();
        $error404->render();
        $baseBottom->render();

    }

    public static function getController() {
        if (isset($_GET["controller"]))
            return self::toCammelCase($_GET["controller"], true)."Controller";
        return "MainController";
    }

    public static function getAction() {
        if (isset($_GET["action"]))
            return self::toCammelCase($_GET["action"], false);
        return "perform";
    }

    public static function getParams() {
        $params = array();
        foreach ($_GET as $name => $value) {
            if ($name != "controller" && $name != "action")
                $params[$name] = $value;
        }
        return $params;
    }

    private static function toCammelCase($target, $isFirstBig) {
        $exploded = explode("-", $target);
        $buildedString = "";
        foreach ($exploded as $word)
            $buildedString .= ucfirst($word);
        if (!$isFirstBig)
            $buildedString = lcfirst($buildedString);
        return $buildedString;
    }

}