<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 04:06
 */

class AuthController {

    public function register() {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Rejestracja");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $register_form = new Template("register_form");
        $baseBottom = new Template("base_bottom");

        // rendering
        $baseTop->render();
        $register_form->render();
        $baseBottom->render();

    }

    public function registerToDb() {

        $form = array(
            "nick" => $_POST["nick"],
            "email" => $_POST["email"],
            "email2" => $_POST["email2"],
            "password" => $_POST["password"],
            "password2" => $_POST["password2"]
        );

        try {
            $this->validate($form);
        } catch (Exception $e) {
            $_SESSION["message"] = $e->getMessage();
            $_SESSION["message-class"] = "danger";
            header("Location: /?controller=auth&action=register");
            die();
        }

        unset($form["email2"]);
        unset($form["password2"]);
        $form["id"] = null;
        $form["password"] = sha1($form["password"]);
        $model = new AuthModel();
        $model->register($form);
        header("Location: /");

    }

    // FIXME sprawdzanie, czy nick lub e-mail istnieje
    private function validate($form) {

        throw new Exception("Taki błąd z dupy :)");

        // sprawdzanie długości loginu
        if (isset($form["nick"])) {
            if (strlen($form["nick"]) < 4 || strlen($form["nick"]) > 12)
                throw new Exception("Długość nicku musi być z przedziału od 4 do 12 znaków.");
        }

        // sprawdzanie poprawności e-maila
        if (isset($form["email"])) {
            if (!filter_var($form["email"], FILTER_VALIDATE_EMAIL))
                throw new Exception("Zły format adresu e-mail.");
        }

        // sprawdzanie, czy maile są te same
        if (isset($form["email2"])) {
            if ($form["email"] !== $form["email2"])
                throw new Exception("Adresy e-mail nie są takie same!");
        }

        // sprawdzanie długości hasła
        if (isset($form["password"])) {
            if (strlen($form["password"]) < 6 || strlen($form["password"]) > 16)
                throw new Exception("Długość hasła musi być z przedziału od 4 do 12 znaków.");
        }

        // sprawdzanie, czy hasła są takie same
        if (isset($form["password2"])) {
            if ($form["password"] !== $form["password2"])
                throw new Exception("Hasła nie są takie same!");
        }

    }

}