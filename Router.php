<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 00:55
 */

class Router {

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