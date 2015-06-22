<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 01:34
 */

class Loader {

    public function __construct() {
        spl_autoload_register(array($this, "importController"));
        spl_autoload_register(array($this, "importModel"));
    }

    private function importModel($className) {
        return $this->import($className, "model", "php");
    }

    private function importController($className) {
        return $this->import($className, "controller", "php");
    }

    private function import($name, $type, $extension) {
        $fullPath = $type."/".$name.".".$extension;
        if (!file_exists($fullPath))
            return false;
        include_once $fullPath;
        return true;
    }

}