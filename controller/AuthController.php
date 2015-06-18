<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 04:06
 */

class AuthController extends Controller {

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

    public function login() {

        //creating and seting
        $baseTop = new Template("base_top");
        $baseTop->setVar("description", "Logowanie");
        $baseTop->setVar("title", $baseTop->getVar("description") . " - WebJ");
        $login_form = new Template("login_form");
        $baseBottom = new Template("base_bottom");

        // rendering
        $baseTop->render();
        $login_form->render();
        $baseBottom->render();

    }

    public function logout() {

        unset($_SESSION["logged"]);
        self::redirect("Wylogowano pomyślnie.", "");

    }

    public function registerToDb() {

        $form = array(
            "email" => $_POST["email"],
            "email2" => $_POST["email2"],
            "password" => $_POST["password"],
            "password2" => $_POST["password2"]
        );

        try {
            $this->validate($form);
        } catch (Exception $e) {
            self::redirect($e->getMessage(), "danger", "auth", "register");
        }

        $model = new AuthModel();
        $isLoginDataCorrect = $model->isRegistered($form["email"]);
        if ($isLoginDataCorrect)
            self::redirect("Użytkownik o podanym adresie e-mail już istnieje.", "danger", "auth", "register");
        $model->register($form);
        self::redirect("Zostałeś zarejestrowany! Możesz się zalogować.", "success");

    }

    public function loginToDb() {

        $form = array(
            "email" => $_POST["email"],
            "password" => $_POST["password"]
        );

        try {
            $this->validate($form);
        } catch (Exception $e) {
            self::redirect($e->getMessage(), "danger", "auth", "login");
        }

        $model = new AuthModel();
        $isLoginDataCorrect = $model->isLoginDataCorrect($form);
        if (!$isLoginDataCorrect)
            self::redirect("Nie istnieje taki użytkownik.", "danger", "auth", "login");
        $_SESSION["logged"] = $form["email"];
        self::redirect("Zostałeś zalogowany!", "success");

    }

    private function validate($form) {

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