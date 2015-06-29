<?php

/**
 * Class AuthModel - klasa typu Model, zarządza danymi autoryzacyjnymi
 */
class AuthModel extends Model {

    /**
     * Konstruktor - wywołuje konstruktor klasy nadrzędnej
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Metoda, która rejestruje użytkownika w aplikacji (zapis do bazy danych)
     * @param $form - pobrany formularz rejestracyjny od użytkownika
     */
    public function register($form) {
        unset($form["email2"]);
        unset($form["password2"]);
        $form["password"] = sha1($form["password"]);
        $this->insert("users", $form);
    }

    /**
     * Metoda sprawdza, czy dane logowania są prawdziwe - in. dokonuje logowania
     * @param $form - pobrany formularz logownia od użytkownika
     * @return mixed - 1 (true), gdy dane są prawidłowe, w przeciwnym wypadku 0 (false)
     */
    public function isLoginDataCorrect($form) {
        $form["password"] = sha1($form["password"]);
        $result = $this->select(
            "users",
            array("count(id)"),
            "email = '" . addslashes($form["email"]) . "' and password = '" . addslashes($form["password"]) . "'"
        );
        return $result[0]["count(id)"];
    }

    /**
     * Metoda która sprawdza, czy użytkownik jest zarejestrowany
     * @param $email - e-mail użytkownika
     * @return mixed - 1 (true), gdy użytkownik istnieje, w przeciwnym wypadku 0 (false)
     */
    public function isRegistered($email) {
        $result = $this->select(
            "users",
            array("count(id)"),
            "email = '" . addslashes($email) . "'"
        );
        return $result[0]["count(id)"];
    }

}