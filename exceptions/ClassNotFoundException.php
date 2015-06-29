<?php

/**
 * Class ClassNotFoundException - wyjątek rzucany, gdy klasa nie zostałą znaleziona
 */
class ClassNotFoundException extends Exception {

    /**
     * Konstruktor - wywołuje konstruktor nadklasy
     */
    public function __construct() {
        parent::__construct();
    }

}