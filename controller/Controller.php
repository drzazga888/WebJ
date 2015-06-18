<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 17.06.15
 * Time: 23:16
 */

abstract class Controller {

    public static function redirect($message = null, $messageClass = null, $controller = "main", $action = "perform") {
        if ($message === null)
            unset($_SESSION["message"]);
        else
            $_SESSION["message"] = $message;
        if ($messageClass === null)
            unset($_SESSION["message-class"]);
        else
            $_SESSION["message-class"] = $messageClass;
        header("Location: /?controller=" . $controller . "&action=" . $action);
        die();
    }

}