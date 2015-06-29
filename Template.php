<?php

/**
 * Class Template - reprezentacja szablony - realizuje funckę widoku w modelu MVC
 */
class Template {

    private $name;
    private $vars = array();

    /**
     * Konstruktor Template - tworzy nowy obiektdo zarządzania szablonem
     * @param $name - nazwa pliku z szablonem, który ma być załadowany
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Metoda renderuje, czyli tworzy wynikowy HTML na podstawie szblonu
     */
    public function render() {
        extract($this->vars);
        include_once "templates/".$this->name.".phtml";
    }

    /**
     * Metoda ustawia zmienną do podmienienia w szablonie
     * @param $name - nazwa zmiennej
     * @param $value - wartość zmiennej
     */
    public function setVar($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * Metoda pobiera ustawioną zmienną do podmienienia w szablonie
     * @param $name - nazwa zmiennej
     * @return mixed - wartość zmiennej
     */
    public function getVar($name) {
        return $this->vars[$name];
    }

    /**
     * Metoda, która służy do dopisania kodu ładującego skrypty w HTML
     * @param $name - nazwa skryptu
     */
    public function loadScript($name) {
        $this->vars["scripts"][] = $name;
    }

}