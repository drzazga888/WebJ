<?php

/**
 * Class FileNotFoundException - wyjątek rzucany, gdy plik nie został znaleziony
 */
class FileNotFoundException extends Exception {

    /**
     * Konstruktor - wywołuje konstruktor nadklasy
     */
    public function __construct() {
        parent::__construct();
    }

}