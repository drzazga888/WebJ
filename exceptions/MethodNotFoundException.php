<?php

/**
 * Class MethodNotFoundException - wyjątek rzucany, gdy metoda nie została znaleziona
 */
class MethodNotFoundException extends Exception {

    /**
     * Konstruktor - wywołuje konstruktor nadklasy
     */
    public function __construct() {
        parent::__construct();
    }

}