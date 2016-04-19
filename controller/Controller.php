<?php

/**
 * Class Controller - abstrakcyjny kontroler, który zawiera metody używne przez inne kontrolery. Kontrolery w modelu MVC mają obsługiwać (przetwarzać) żądanie
 */
abstract class Controller {

    /**
     * Funkcja, która ma za zadanie przekierowywać na inny adres, potrafi ustawiać także odpowiedni komunika dla użytkownika
     * @param null $message - wiadomość dla użytkownika
     * @param null $messageClass - wydźwięk wiadomości - gdy jest null, to komunikat jest normalnej ważności. Gdy jest "success", to komunikat ma dobry wydźwięk :). Gdy jest "danger", komunikat ma zły wydźwięk
     * @param string $controller - nazwa kontrolera do utworzenia (bez cammel-case'a)
     * @param string $action - nazwa akcji / metody kontrolera do wywołania (bez cammel-case'a)
     * @param array $params - dodatkowe parametry wywołania akcji
     */
    public static function redirect($message = null, $messageClass = null, $controller = "main", $action = "perform", $params = array()) {
        if ($message === null)
            unset($_SESSION["message"]);
        else
            $_SESSION["message"] = $message;
        if ($messageClass === null)
            unset($_SESSION["message-class"]);
        else
            $_SESSION["message-class"] = $messageClass;
        $url = "/?controller=" . $controller . "&action=" . $action;
        foreach ($params as $name => $value) {
            $url .= '&' . $name . '=' . $value;
        }
        header("Location: " . $url);
        die();
    }

}