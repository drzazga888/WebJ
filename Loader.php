<?php

/**
 * Class Loader - rejestruje funkcjie autołaujące klasy typu Controller i Model
 */
class Loader {

    /**
     * Konstruktor rejestrujący funkcje autoładujące
     */
    public function __construct() {
        spl_autoload_register(array($this, "importController"));
        spl_autoload_register(array($this, "importModel"));
        spl_autoload_register(array($this, "throwFileNotFoundException"));
    }

    /**
     * Fukcja, które jest podpięta pod funckę autoładującą spl_autoload_register w celu rzucenia wyjątku braku znalezionego pliku
     * @param $className - nazwa klasy do dołączenia
     * @throws FileNotFoundException - wyjątek oznaczający brak pliku
     */
    private function throwFileNotFoundException($className) {
        throw new FileNotFoundException;
    }

    /**
     * Funkcja podpięta pod spl_autoload_register, ładuje ewentualą klasę typu Model
     * @param $className - nazwa klasy
     * @return bool - true, gdy klasa została załadowana, false w przeciwnym wypadku
     */
    private function importModel($className) {
        return $this->import($className, "model", "php");
    }

    /**
     * Funkcja podpięta pod spl_autoload_register, ładuje ewentualą klasę typu Controller
     * @param $className - nazwa klasy
     * @return bool - true, gdy klasa została załadowana, false w przeciwnym wypadku
     */
    private function importController($className) {
        return $this->import($className, "controller", "php");
    }

    /**
     * Funkcja pomocnicza do ładowania klas
     * @param $name - nazwa klasy
     * @param $type - typ klasy (identyfikacja folderu, do którego należy się udać w poszukiwaniu)
     * @param $extension - rozszerzenie ładowanego pliku
     * @return bool - true, gdy klasa została załadowana, false w przeciwnym wypadku
     */
    private function import($name, $type, $extension) {
        $fullPath = $type."/".$name.".".$extension;
        if (!file_exists($fullPath))
            return false;
        include_once $fullPath;
        return true;
    }

}