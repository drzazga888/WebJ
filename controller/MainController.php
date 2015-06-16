<?php
/**
 * Created by PhpStorm.
 * User: kamil
 * Date: 09.06.15
 * Time: 13:35
 */

class MainController {

    function perform() {
        $view = new Template("base");
        $view->setVar("title", "działam :)");
        $view->render();
    }

}