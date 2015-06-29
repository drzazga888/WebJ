<?php

/**
 * Class Router - dostarcza informacje o nazwie klasy typu Controller, nazwę metody którą trzeba wykonać i parametry tej funkcji
 */
class Router {

    /**
     * Funkcja wyświetla komunikat z błędem
     */
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

    /**
     * Funkcja pobiera nazwę Kontrolera z adresu
     * @return string - nazwa Kontrolera
     */
    public static function getController() {
        if (isset($_GET["controller"]))
            return self::toCammelCase($_GET["controller"], true)."Controller";
        return "MainController";
    }

    /**
     * Funckja pobiera nazwę metody z Kontrolera z adresu
     * @return string  - nazwa akcji do wykonania na Kontrolerze
     */
    public static function getAction() {
        if (isset($_GET["action"]))
            return self::toCammelCase($_GET["action"], false);
        return "perform";
    }

    /**
     * Funckja tworzy tablicę asoscjacyjną arumentów dla akcji
     * @return array - tablica z parametrami
     */
    public static function getParams() {
        $params = array();
        foreach ($_GET as $name => $value) {
            if ($name != "controller" && $name != "action")
                $params[$name] = $value;
        }
        return $params;
    }

    /**
     * Funckja pomocnicza która zamienia strina na jefo reperezentację w CammelCase np "cos-tam" zamienia na "CosTam"
     * @param $target - string do zamienienia
     * @param $isFirstBig - czy pierwsza litera ma być wielka
     * @return string - przetworzony napis
     */
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