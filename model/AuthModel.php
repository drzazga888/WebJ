<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 03:00
 */

class AuthModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function register($form) {
        unset($form["email2"]);
        unset($form["password2"]);
        $form["password"] = sha1($form["password"]);
        $this->insert("users", $form);
    }

    public function isLoginDataCorrect($form) {
        $form["password"] = sha1($form["password"]);
        $result = $this->select(
            "users",
            array("count(id)"),
            "email = '" . addslashes($form["email"]) . "' and password = '" . addslashes($form["password"]) . "'"
        );
        return $result[0]["count(id)"];
    }

    public function isRegistered($email) {
        $result = $this->select(
            "users",
            array("count(id)"),
            "email = '" . addslashes($email) . "'"
        );
        return $result[0]["count(id)"];
    }

}