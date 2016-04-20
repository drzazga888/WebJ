<?php

/**
 * Class EmptySongFoundException - wyjątek rzucany, gdy utwór nie posiada żadnych sampli
 */
class EmptySongException extends Exception {

    /**
     * Konstruktor - wywołuje konstruktor nadklasy
     */
    public function __construct() {
        parent::__construct();
    }

}